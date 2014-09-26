#!/bin/bash
DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )"
lessc $DIR/../assets/less/application.less assets/css/application.css
lessc $DIR/../assets/less/report.less assets/css/report.css
lessc $DIR/../assets/less/claim.less assets/css/claim.css
