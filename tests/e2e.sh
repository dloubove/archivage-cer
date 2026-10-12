#!/usr/bin/env bash
# Test bout en bout de l'application via HTTP (serveur PHP intégré + SQLite, sans MySQL).
# Usage : bash tests/e2e.sh        (nécessite php et curl)
set -u
cd "$(dirname "$0")/.."
export E2E_DB="$(mktemp -u).sqlite"
PORT=8765
BASE="http://127.0.0.1:$PORT"
JAR="$(mktemp)"
ok=0; ko=0
check() { if [ "$2" = "1" ]; then ok=$((ok+1)); echo "  [OK] $1"; else ko=$((ko+1)); echo "  [ÉCHEC] $1"; fi; }
has() { grep -q -- "$1" <<<"$2" && echo 1 || echo 0; }
token() { curl -s -b "$JAR" -c "$JAR" "$BASE/$1" | grep -o 'name="csrf_token" value="[a-f0-9]*"' | head -1 | sed 's/.*value="//;s/"//'; }
code() { curl -s -o /dev/null -w '%{http_code}' "$@"; }

php -d auto_prepend_file="$PWD/tests/e2e_prepend.php" -S 127.0.0.1:$PORT -t public public/router.php >/tmp/e2e_server.log 2>&1 &
SERVER=$!
trap 'kill $SERVER 2>/dev/null; rm -f "$JAR" "$E2E_DB"' EXIT
sleep 1

echo "Accès et protection des pages"
check "/dashboard sans session redirige vers /login" "$([ "$(code "$BASE/index.php?url=dashboard")" = 302 ] && echo 1 || echo 0)"
check "page inconnue : 404" "$([ "$(code "$BASE/index.php?url=nimportequoi")" = 404 ] && echo 1 || echo 0)"
TOKEN=$(token "index.php?url=register")
check "formulaire d'inscription contient un token CSRF" "$([ -n "$TOKEN" ] && echo 1 || echo 0)"

echo "CSRF"
check "POST sans token refusé (403)" "$([ "$(code -b "$JAR" -c "$JAR" -d 'nom=Eve&email=eve@x.cm&mot_de_passe=motdepasse1' "$BASE/index.php?url=register")" = 403 ] && echo 1 || echo 0)"
check "POST avec faux token refusé (403)" "$([ "$(code -b "$JAR" -c "$JAR" -d 'csrf_token=faux&nom=Eve&email=eve@x.cm&mot_de_passe=motdepasse1' "$BASE/index.php?url=register")" = 403 ] && echo 1 || echo 0)"

echo "Inscription / connexion"
OUT=$(curl -s -b "$JAR" -c "$JAR" -d "csrf_token=$TOKEN&nom=Eve&email=eve@x.cm&mot_de_passe=court" "$BASE/index.php?url=register")
check "mot de passe trop court refusé" "$(has '8 caractères' "$OUT")"
OUT=$(curl -s -b "$JAR" -c "$JAR" -d "csrf_token=$TOKEN&nom=Eve&email=pas-un-email&mot_de_passe=motdepasse1" "$BASE/index.php?url=register")
check "email invalide refusé" "$(has "pas valide" "$OUT")"
CODE=$(code -b "$JAR" -c "$JAR" -d "csrf_token=$TOKEN&nom=Eve&email=eve@x.cm&mot_de_passe=motdepasse1" "$BASE/index.php?url=register")
check "inscription valide : redirection (302)" "$([ "$CODE" = 302 ] && echo 1 || echo 0)"
OUT=$(curl -s -b "$JAR" -c "$JAR" "$BASE/index.php?url=dashboard")
check "tableau de bord accessible, nom affiché" "$(has 'Bienvenue, Eve' "$OUT")"

echo "CRUD des CERs (+ XSS)"
TOKEN=$(token "index.php?url=archive/create")
XSS="<script>alert(1)</script>"
curl -s -o /dev/null -b "$JAR" -c "$JAR" --data-urlencode "csrf_token=$TOKEN" --data-urlencode "titre=$XSS" --data-urlencode "contenu=Contenu" "$BASE/index.php?url=archive"
OUT=$(curl -s -b "$JAR" -c "$JAR" "$BASE/index.php?url=archive")
check "CER créé et listé" "$(has 'CER archivé' "$OUT")"
check "XSS : le script est échappé dans la page" "$([ "$(has '&lt;script&gt;alert(1)' "$OUT")" = 1 ] && [ "$(has '<script>alert(1)' "$OUT")" = 0 ] && echo 1 || echo 0)"
check "page d'édition accessible" "$([ "$(code -b "$JAR" "$BASE/index.php?url=archive/1/edit")" = 200 ] && echo 1 || echo 0)"
check "CER inexistant : 404" "$([ "$(code -b "$JAR" "$BASE/index.php?url=archive/999/edit")" = 404 ] && echo 1 || echo 0)"
TOKEN=$(token "index.php?url=archive/1/edit")
curl -s -o /dev/null -b "$JAR" -c "$JAR" --data-urlencode "csrf_token=$TOKEN" --data-urlencode "titre=Titre modifié" --data-urlencode "contenu=Nouveau" "$BASE/index.php?url=archive/1/update"
OUT=$(curl -s -b "$JAR" -c "$JAR" "$BASE/index.php?url=archive")
check "CER modifié" "$(has 'Titre modifié' "$OUT")"
TOKEN=$(token "index.php?url=archive")
curl -s -o /dev/null -b "$JAR" -c "$JAR" -d "csrf_token=$TOKEN" "$BASE/index.php?url=archive/1/delete"
OUT=$(curl -s -b "$JAR" -c "$JAR" "$BASE/index.php?url=archive")
check "CER supprimé" "$(has 'Aucun CER' "$OUT")"

echo "Corbeilles"
TOKEN=$(token "index.php?url=corbeilles/create")
curl -s -o /dev/null -b "$JAR" -c "$JAR" -d "csrf_token=$TOKEN&nom=Algèbre&description=" "$BASE/index.php?url=corbeilles"
OUT=$(curl -s -b "$JAR" -c "$JAR" "$BASE/index.php?url=corbeilles")
check "corbeille créée et listée" "$(has 'Algèbre' "$OUT")"

echo "Déconnexion"
TOKEN=$(token "index.php?url=dashboard")
curl -s -o /dev/null -b "$JAR" -c "$JAR" -d "csrf_token=$TOKEN" "$BASE/index.php?url=logout"
check "après déconnexion, /dashboard redirige" "$([ "$(code -b "$JAR" -c "$JAR" "$BASE/index.php?url=dashboard")" = 302 ] && echo 1 || echo 0)"
TOKEN=$(token "index.php?url=login")
CODE=$(code -b "$JAR" -c "$JAR" -d "csrf_token=$TOKEN&email=eve@x.cm&mot_de_passe=motdepasse1" "$BASE/index.php?url=login")
check "reconnexion avec les bons identifiants" "$([ "$CODE" = 302 ] && echo 1 || echo 0)"

echo; echo "Résultat e2e : $ok réussis, $ko échec(s)"
[ "$ko" -eq 0 ]
