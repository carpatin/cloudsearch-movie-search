
set -e;

if [[ "$#" -ne 1 ]]; then
    echo "Usage: ${BASH_SOURCE[0]} <domain-name>"
    exit 1
fi

domainName=$1
baseDir="$(cd "$(dirname "${BASH_SOURCE[0]}")" > /dev/null && pwd)"

aws cloudsearch update-service-access-policies \
    --domain-name ${domainName} \
    --access-policies "`cat "${baseDir}"/config/access-policies.json`"