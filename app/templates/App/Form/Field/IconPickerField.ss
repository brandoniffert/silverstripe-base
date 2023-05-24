<div class="iconpickerfield-wrapper">
  <div class="iconpickerfield-ss-field">
    <select $AttributesHTML>
      <% loop $Options %>
        <option value="$Value.XML"
          <% if $Selected %> selected="selected"<% end_if %>
          <% if $Disabled %> disabled="disabled"<% end_if %>
        ><% if $Title.exists %>$Title.XML<% else %>&nbsp;<% end_if %>
        </option>
      <% end_loop %>
    </select>
  </div>

  <div class="iconpickerfield-field">
    <div class="iconpickerfield-preview">
      <div class="iconpickerfield-selected" style="visibility: hidden">
        $SelectedItem
      </div>
    </div>

    <div class="iconpickerfield-controls">
      <button type="button" class="iconpickerfield-choose iconpickerfield-trigger">Choose</button>
      <button type="button" class="iconpickerfield-change iconpickerfield-trigger">Change</button>
      <button type="button" class="iconpickerfield-remove">Remove</button>
    </div>
  </div>

  <div class="iconpickerfield-holder">
    <div class="iconpickerfield-search">
      <input type="text" placeholder="Filter">
      <button class="iconpickerfield-search-clear" type="button">Clear Filter</button>
    </div>

    <ul class="iconpickerfield-icon-list">
      $IconList
    </ul>
  </div>
</div>
