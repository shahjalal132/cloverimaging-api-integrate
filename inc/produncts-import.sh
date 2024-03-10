i=0
while [ $i -le 9 ]; do
    i=$(($i + 1))
    echo "adding product no: $i ..."
    curl -H 'Cache-Control: no-cache' "http://clover-api-integrate.test/product_insert_wooco/?$(date +%s)" >>/dev/null
    echo "$i th product added"
done
