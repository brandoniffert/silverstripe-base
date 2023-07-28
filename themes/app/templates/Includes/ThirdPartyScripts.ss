<% if Position == 'Head' %>
  <% loop SiteConfig.HeadThirdPartyScripts %>
    $Code.Raw
  <% end_loop %>
<% end_if %>

<% if Position == 'Body' %>
  <% loop SiteConfig.BodyThirdPartyScripts %>
    $Code.Raw
  <% end_loop %>
<% end_if %>
