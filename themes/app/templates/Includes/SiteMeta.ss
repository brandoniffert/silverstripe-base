<meta http-equiv="x-ua-compatible" content="ie=edge">
<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

<link rel="apple-touch-icon" sizes="180x180" href="$Asset(/favicon/apple-touch-icon.png)">
<link rel="icon" type="image/png" sizes="32x32" href="$Asset(/favicon/favicon-32x32.png)">
<link rel="icon" type="image/png" sizes="16x16" href="$Asset(/favicon/favicon-16x16.png)">
<link rel="manifest" href="$Asset(/favicon/manifest.json)">
<meta name="msapplication-TileColor" content="#ffffff">
<meta name="theme-color" content="#ffffff">

$MetaTags(false)

<% if ObjectMetaTags %>
  <title>$ObjectMetaTags.MetaTitle | $SiteConfig.Title</title>
  <meta property="og:title" content="$ObjectMetaTags.MetaTitle | $SiteConfig.Title" />
  <meta name="twitter:title" content="$ObjectMetaTags.MetaTitle | $SiteConfig.Title">

  <% if ObjectMetaTags.MetaDescription %>
    <meta name="description" content="$ObjectMetaTags.MetaDescription">
    <meta property="og:description" content="$ObjectMetaTags.MetaDescription" />
    <meta name="twitter:description" content="$ObjectMetaTags.MetaDescription">
  <% end_if %>

  <% if ObjectMetaTags.MetaImage %>
    <meta property="og:image" content="$ObjectMetaTags.MetaImage.AbsoluteURL">
    <meta name="twitter:image" content="$ObjectMetaTags.MetaImage.AbsoluteURL">
  <% end_if %>
<% else %>
  <title><% if MetaTitle %>$MetaTitle<% else %>$Title<% end_if %> | $SiteConfig.Title</title>
  <meta property="og:title" content="<% if MetaTitle %>$MetaTitle<% else %>$Title<% end_if %> | $SiteConfig.Title" />
  <meta name="twitter:title" content="<% if MetaTitle %>$MetaTitle<% else %>$Title<% end_if %> | $SiteConfig.Title">

  <% if MetaDescription %>
    <meta name="description" content="$MetaDescription">
    <meta property="og:description" content="$MetaDescription">
    <meta name="twitter:description" content="$MetaDescription">
  <% end_if %>
<% end_if %>

<meta property="og:url" content="$AbsoluteLink" />

<% if SiteConfig.SocialSharePhoto.Exists && not ObjectMetaTags.MetaImage %>
  <meta property="og:image" content="$SiteConfig.SocialSharePhoto.AbsoluteURL">
  <meta name="twitter:image" content="$SiteConfig.SocialSharePhoto.AbsoluteURL">
<% end_if %>

<% if StructuredData %>
  <script type="application/ld+json">
    $StructuredData.RAW
  </script>
<% end_if %>
