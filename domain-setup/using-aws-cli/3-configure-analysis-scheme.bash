
set -e;

if [[ "$#" -ne 1 ]]; then
    echo "Usage: ${BASH_SOURCE[0]} <domain-name>"
    exit 1
fi

domainName=$1
baseDir="$(cd "$(dirname "${BASH_SOURCE[0]}")" > /dev/null && pwd)"

for file in "${baseDir}"/config/analysis-scheme/*
do
    if [[ -f ${file} ]]; then
        echo "Defining analysis scheme using json file: ${file}"
        aws cloudsearch define-analysis-scheme --domain ${domainName} --analysis-scheme "`cat "${file}"`"
    fi
done
