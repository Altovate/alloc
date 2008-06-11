#!/bin/bash

cwd="${0%/*}/"
cd $cwd

BACKUP_DIR="$(cat alloc_config.php  | grep ATTACHMENTS_DIR  | sed -e 's/define("ATTACHMENTS_DIR","//' | sed -e 's/");$//')";

if [ -d "${BACKUP_DIR}" ]; then
  file="${BACKUP_DIR}whatsnew/0/$(date +%Y-%m-%d)"
  info=$(echo "${1}" | sed -e 's/^[^\*]*$//' | sed -e 's/ \* /<li>/')
  echo $info >> $file
fi

