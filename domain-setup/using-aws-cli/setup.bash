
set -e;

if [[ "$#" -ne 1 ]]; then
    echo "Usage: ${BASH_SOURCE[0]} <domain-name>"
    exit 1
fi

domainName=$1
baseDir="$(cd "$(dirname "${BASH_SOURCE[0]}")" > /dev/null && pwd)"

# Create the domain
bash "${baseDir}"/1-create-domain.bash ${domainName}

# Allow access to the newly created domain
bash "${baseDir}"/2-configure-access-policies.bash ${domainName}

# Add all the analysis schemes from config/analysis-scheme to the new domain
bash "${baseDir}"/3-configure-analysis-scheme.bash ${domainName}

# Configure the fields
bash "${baseDir}"/4-configure-fields.bash ${domainName}

# Finally trigger documents reindex that is required after any schema change
bash "${baseDir}"/5-trigger-indexing.bash ${domainName}

