<% if ActionType = 'Internal' %>
  <a
    href="$ActionLink"
    class="$Classes $ExtraClass"
    $ActionAttrs
    <% if AriaHidden %>tabindex="-1" <% end_if %>
  >
    <% if Icon %>
      $Icon.WithClasses('w-7 mr-4 flex-shrink-0')
    <% end_if %>

    <span>$Title</span>
  </a>
<% end_if %>

<% if ActionType = 'Anchor' %>
  <a
    href="$ActionLink"
    class="$Classes $ExtraClass"
    data-scroll="$AnchorTarget"
    $ActionAttrs
    <% if AriaHidden %>tabindex="-1"<% end_if %>
  >
    <% if Icon %>
      $Icon.WithClasses('w-7 mr-4 flex-shrink-0')
    <% end_if %>

    <span>$Title</span>
  </a>
<% end_if %>

<% if ActionType = 'External' %>
  <a
    href="$ActionLink"
    class="$Classes $ExtraClass"
    $ActionAttrs
    <% if AriaHidden %>tabindex="-1"<% end_if %>
  >
    <% if Icon %>
      $Icon.WithClasses('w-7 mr-4 flex-shrink-0')
    <% end_if %>

    <span>$Title</span>
  </a>
<% end_if %>

<% if ActionType = 'Email' %>
  <a
    href="$ActionLink"
    class="$Classes $ExtraClass"
    $ActionAttrs
    <% if AriaHidden %>tabindex="-1"<% end_if %>
  >
    <% if Icon %>
      $Icon.WithClasses('w-7 mr-4 flex-shrink-0')
    <% end_if %>

    <span>$Title</span>
  </a>
<% end_if %>

<% if ActionType = 'File' %>
  <a
    href="$ActionLink"
    class="$Classes $ExtraClass"
    $ActionAttrs
    <% if AriaHidden %>tabindex="-1"<% end_if %>
  >
    <% if Icon %>
      $Icon.WithClasses('w-7 mr-4 flex-shrink-0')
    <% end_if %>

    <span>$Title</span>
  </a>
<% end_if %>

<% if ActionType = 'Video' %>
  <a
    href="$ActionLink"
    class="$Classes $ExtraClass"
    data-modal-title="$Title"
    $ActionAttrs
    <% if AriaHidden %>tabindex="-1"<% end_if %>
  >
    <% if Icon %>
      $Icon.WithClasses('w-7 mr-4 flex-shrink-0')
    <% end_if %>

    <span>$Title</span>
  </a>
<% end_if %>

<% if ActionType = 'Element' && ActionElement.Exists %>
  <a
    href="$ActionLink"
    class="$Classes $ExtraClass"
    $ActionAttrs
    <% if AriaHidden %>tabindex="-1"<% end_if %>
  >
    <% if Icon %>
      $Icon.WithClasses('w-7 mr-4 flex-shrink-0')
    <% end_if %>

    <span>$Title</span>
  </a>
<% end_if %>
