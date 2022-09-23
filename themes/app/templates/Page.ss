<!doctype html>
<html lang="$ContentLocale" class="_preload">

<head>
  <% base_tag %>
  <% include SiteMeta %>
  <% include GTM GTMID=$SiteConfig.GTMID %>
  $SiteCSS
  $SiteJS
</head>

<body class="$BodyClasses antialiased">
  <% include GTM NoScript="true", GTMID=$SiteConfig.GTMID %>

  <a href="#main-content" class="skip-link">Skip to main content</a>

  <% include SiteHeader %>

  <main id="main-content">
    $Layout
  </main>

  <% include SiteFooter %>
</body>

</html>
