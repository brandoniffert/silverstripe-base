<% if Actions.Count %>
  <div class="$Classes">
    <% loop Actions %>
      <% include CallToAction ExtraClass=$Up.ExtraClass %>
    <% end_loop %>
  </div>
<% end_if %>
