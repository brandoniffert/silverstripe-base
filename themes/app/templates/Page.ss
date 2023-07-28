<!doctype html>
<html lang="$ContentLocale" class="_preload">

<head>
  <% base_tag %>
  <% include SiteMeta %>
  <% include GTM GTMID=$SiteConfig.GTMID %>
  $SiteCSS
  $SiteJS
  <% include ThirdPartyScripts Position="Head" %>
</head>

<body class="$BodyClasses antialiased">
  <% include GTM NoScript="true", GTMID=$SiteConfig.GTMID %>

  <a href="#main-content" class="skip-link">Skip to main content</a>

  <span class="absolute top-0 scroll-pixel" aria-hidden="true"></span>

  <% include SiteHeader %>

  <main id="main-content">
    $Layout
  </main>

  <% include SiteFooter %>
  <% include MobileBar %>
  <% include ThirdPartyScripts Position="Body" %>
</body>

</html>
