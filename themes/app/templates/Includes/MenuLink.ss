<a href="$Link<% if AnchorTarget %>$AnchorTarget<% end_if %>" title="$MenuTitle" <% if Role %>role="$Role"<% end_if %> class="$Classes" <% if IsExternalRedirector %>target="_blank" rel="noopener noreferrer"<% end_if %>>
  <% if MenuTitleOverride %>
    $MenuTitleOverride
  <% else %>
    $MenuTitle
  <% end_if %>
</a>
