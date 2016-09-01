# magento-module-rbm-template
![Alt text](README/screenshots/img-info.jpg?raw=true "Template Information")
![Alt text](README/screenshots/img-content.jpg?raw=true "Template Content")
![Alt text](README/screenshots/img-conditions.jpg?raw=true "Template Conditions")


# Google shopping rss 2.0
```xml
<?xml version="1.0" encoding="UTF-8"?>
<rss xmlns:g="http://base.google.com/ns/1.0" version="2.0">
    <channel>
        <title><![CDATA[{{title}}]]></title>
        <link><![CDATA[{{url}}]]></link>  
        <description><![CDATA[{{description}}]]></description>
        {{foreach collection as="product"}}
        <item>
            <!-- The following attributes are always required -->
            <g:id><![CDATA[{{var product.getId()}}]]></g:id> <!--one comment more -->
            <g:title><![CDATA[{{var product.getName()}}]]></g:title>
            <g:description><![CDATA[{{var product.getShortDescription()}}]]></g:description>
            <g:link><![CDATA[{{var product.getProductUrl()}}]]></g:link>
            <g:image_link><![CDATA[{{var product.getImageUrl()}}]]></g:image_link>
            <g:condition><![CDATA[new]]></g:condition>
            {{if product.isInStock() && product.isSaleable()}}
              <g:availability><![CDATA[in stock]]></g:availability>
            {{else}}
              <g:availability><![CDATA[out of stock]]></g:availability>
            {{/if}}
            <g:price><![CDATA[{{var product.getFinalPrice()}} BRL]]> </g:price>  			
            <!-- 2 of the following 3 attributes are required fot this item according to the Unique product Identifier Rules -->
            <g:gtin><![CDATA[{{var product.getSku()}}]]></g:gtin>
            <g:brand><![CDATA[{{var product.getBrand()}}]]></g:brand>

            <g:mpn><![CDATA[{{var product.getMpn()}}]]></g:mpn>  			

            <!-- Using helper -->
            <g:google_product_category>
                <![CDATA[{{helper type="rbmTemplate/product/getCategoryFullpath" on="$product" separator="&gt;"}}]]>
            </g:google_product_category>
            <!-- you can use literal-->
            <g:product_type><![CDATA[Some product type]]></g:product_type>
        </item>
        {{/foreach}}
    </channel>
</rss>
```
