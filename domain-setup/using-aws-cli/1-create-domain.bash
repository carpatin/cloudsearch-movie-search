set -e;

if [[ "$#" -ne 1 ]]; then
    echo "Usage: ${BASH_SOURCE[0]} <domain-name>"
    exit 1
fi

domainName=$1

aws cloudsearch create-domain --domain-name ${domainName}
