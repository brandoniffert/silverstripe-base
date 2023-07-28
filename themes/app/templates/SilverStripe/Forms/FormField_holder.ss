<div id="$HolderID" class="field<% if $extraClass %> $extraClass<% end_if %> form-field-wrapper" <% if Required %>data-required="true"<% end_if %>>
  <div class="form-field">
    <% if $Title %>
      <label for="$ID">
        <span>
          $Title
        </span>
      </label>
    <% end_if %>

    $Field
  </div>

  <% if $RightTitle %><div class="form-field-additional">$RightTitle</div><% end_if %>
  <% if $Description %><div class="form-field-description">$Description</div><% end_if %>
  <% if $Message %><span class="form-field-message $MessageType">$Message</span><% end_if %>
</div>
