#!/bin/bash

doxygenConfig=./Doxyfile
doxygenBinary=`which doxygen`
doxygenOutputDir="html"

[[ -z "$doxygenBinary" ]] && echo "Can't find doxygen binary! EXITING!" && exit 1
[[ -z "$doxygenConfig" ]] && echo "Can't find doxygen config! EXITING!" && exit 1

[[ -d $doxygenOutputDir ]] && rm -rf $doxygenOutputDir

$doxygenBinary $doxygenConfig

