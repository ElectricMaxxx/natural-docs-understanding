#!/usr/bin/env bash

find  /data/docs -maxdepth 3 -type f -exec echo File: {}  \; -exec php bin/console rasa:nlu:parse-file --project docs_belittling_text --file {} \;