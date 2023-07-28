<% with SiteConfig.MobileBar %>
  <% if Enabled && Items.Count %>
    <div class="mobile-bar" style="background-color: $BgColor; color: $FgColor">
      <% loop Items %>
        <a href="$ActionLink" class="flex flex-col justify-center items-center px-1 my-auto font-semibold" <% if IsExternalActionLink %>target="_blank" rel="noopener noreferrer"<% end_if %>>
          <% if Icon %>
            $Icon.WithClasses('h-5 mx-auto')
          <% end_if %>

          <% if not HideTitle %>
            <span class="block mt-2">$Title</span>
          <% end_if %>
        </a>
      <% end_loop %>
    </div>
  <% end_if %>
<% end_with %>
