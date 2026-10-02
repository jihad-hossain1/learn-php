#!/bin/bash

API_URL="http://localhost:8000/api/auth/reset-password"
# API_URL="http://localhost:8000/api/auth/forgot-password"
# API_URL="http://localhost:8000/api/auth/login"
# API_URL="http://localhost:8000/api/auth/verify"
# API_URL="http://localhost:8000/api/auth/register"

read -r -d '' BODY <<EOF
{
  "email": "abc2@gmail.com",
  "otp": "258302",
  "password": "123456"
}
EOF

response=$(curl -s -X POST "$API_URL" \
  -H "Content-Type: application/json" \
  -d "$BODY")

# Create directory if missing
mkdir -p ./_res

FILE_NAME="run-$(date +%H_%M_%S).json"
echo "$response" > "./_res/$FILE_NAME"

echo "Saved response to ./_res/$FILE_NAME"

cat ./_res/$FILE_NAME