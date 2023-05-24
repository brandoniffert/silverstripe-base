<% if Image %>
  <img
    <% if Width && Height %>
      src="$Image.FocusFill($Width, $Height).URL"
    <% else %>
      src="$Image.URL"
    <% end_if %>
    <% if not NoLazy %>loading="lazy"<% end_if %>
    width="<% if Width %>$Width<% else %>$Image.Width<% end_if %>"
    height="<% if Height %>$Height<% else %>$Image.Height<% end_if %>"
    $Image.SrcSet($Width, $Height)
    sizes="<% if Sizes %>$Sizes<% else %>(max-width: 1280px) 100vw, 1340px<% end_if %>"
    style="object-position: $Image.FocusPoint.PercentageX% $Image.FocusPoint.PercentageY%; <% if Styles %>$Styles<% end_if %>"
    alt="<% if Alt %>$Alt<% else %>$Image.Title<% end_if %>"
    <% if Classes %>class="$Classes"<% end_if %>
  />
<% end_if %>
