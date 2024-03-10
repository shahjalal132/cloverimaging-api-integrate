i=0
while [ $i -le 9 ]; do
    i=$(($i + 1))
    echo "adding product no: $i ..."
    curl -H 'Cache-Control: no-cache' "http://woo-projects.test/clover-product-insert-to-woocommerce/?$(date +%s)" >>/dev/null
    echo "$i th product added"
done
