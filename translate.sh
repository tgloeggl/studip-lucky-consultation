#!/bin/bash

PO=locale/en/LC_MESSAGES/luckyconsultation.po
POTPHP=locale/en/LC_MESSAGES/luckyconsultation_php.pot
POTJS=locale/en/LC_MESSAGES/luckyconsultation_js.pot
POT=locale/en/LC_MESSAGES/luckyconsultation.pot
MO=locale/en/LC_MESSAGES/luckyconsultation.mo

rm -f $POT
rm -f $POTPHP

find * \( -iname "*.php" -o -iname "*.ihtml" \) | xargs xgettext --from-code=UTF-8 --add-location=full --package-name=LuckyConsultation --language=PHP -o $POTPHP

msgcat $POTJS $POTPHP -o $POT
msgmerge $PO $POT -o $PO
msgfmt $PO --output-file=$MO
