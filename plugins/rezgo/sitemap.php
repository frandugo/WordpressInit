<?php 
	// any new page must start with the page_header, it will include the correct files
	// so that the rezgo parser classes and functions will be available to your templates
	require('rezgo/include/page_header.php');
	
	// start a new instance of RezgoSite
	$site = new RezgoSite();
 
	header("Content-type: text/xml");
	echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
	
	$now = date("c", strtotime('now'));
?>

<urlset xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9 http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd" xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">	
	<url>
		<loc>http://<?php echo $_SERVER[HTTP_HOST]; ?>/</loc>
		<lastmod><?php echo $now; ?></lastmod>
		<changefreq>daily</changefreq>
		<priority>1.0</priority>
	</url>
	<url>
		<loc>http://<?php echo $_SERVER[HTTP_HOST]; ?>/about</loc>
		<lastmod><?php echo $now; ?></lastmod>
		<changefreq>weekly</changefreq>
		<priority>0.6</priority>
	</url>
	<url>
		<loc>http://<?php echo $_SERVER[HTTP_HOST]; ?>/terms</loc>
		<lastmod><?php echo $now; ?></lastmod>
		<changefreq>weekly</changefreq>
		<priority>0.6</priority>
	</url>
	<url>
		<loc>http://<?php echo $_SERVER[HTTP_HOST]; ?>/contact</loc>
		<lastmod><?php echo $now; ?></lastmod>
		<changefreq>weekly</changefreq>
		<priority>0.6</priority>
	</url>
	
	<?php foreach( $site->getTours() as $item ): ?>	
	<url>
		<loc>http://<?php echo $_SERVER[HTTP_HOST]; ?>/details/<?php echo $item->uid; ?>/<?php echo $site->seoEncode($item->item); ?></loc>
		<lastmod><?php echo date("c", (int) $item->updated); ?></lastmod>
		<changefreq>daily</changefreq>
		<priority>1.0</priority>
	</url>
	<?php endforeach; ?>
</urlset>