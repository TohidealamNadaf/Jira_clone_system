#!/bin/bash

# Test the API endpoint with curl and verbose output
echo "Testing /api/v1/issue-types endpoint..."
echo ""

curl -v -w "\nHTTP Status: %{http_code}\n" \
  "http://localhost:8081/jira_clone_system/public/api/v1/issue-types" \
  2>&1

echo ""
echo "Done."
