<% if not Element.HideIf %>
  <section class="$HolderClasses $ExtraClass" id="<% if AnchorOverride %>$AnchorOverride<% else %>$Anchor<% end_if %>" <% if ScrollOffset %>data-scroll-offset="$ScrollOffset"<% end_if %> <% if DisableAnimations %>data-disable-animations<% end_if %>>
    $Element
  </section>
<% end_if %>
