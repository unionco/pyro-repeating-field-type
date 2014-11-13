(function() {

    var sortRepeatingFields = function() {
        $('[data-field-group]').each(function(fieldGroupSortOrder) {

            var $fieldGroup = $(this);
            var repeatingFieldGroupName = $fieldGroup.closest('[data-repeating-field]').attr('data-repeating-field');
            var groupSlug = $fieldGroup.attr('data-field-group');
            var $inputs = $fieldGroup.find('[name^=' + repeatingFieldGroupName + ']');

            $inputs.each(function() {
                var $input = $(this);
                var fieldSlug = $input.closest('[data-field-slug]').attr('data-field-slug');
                var fieldValue = repeatingFieldGroupName + "[" + fieldGroupSortOrder + "][" + groupSlug + "][" + fieldSlug + "]";

                $input.attr('name', fieldValue);
            });
        });
    };

    $(document).on('click', '[data-repeating-group-add]', function(e) {

        e.preventDefault();

        var $clicked = $(this);
        var groupID = $clicked.attr('data-repeating-group-id');
        var groupSlug = $clicked.attr('data-repeating-group-add');
        var $groupTemplates = $($('[data-repeating-template="' + groupID + '"]').html());
        var $groupInputs = $groupTemplates.filter('[data-field-group="' + groupSlug + '"]');
        var $groupTarget = $('[data-repeating-element-wrap="repeating-element-' + groupID + '"]');

        $groupTarget.append($groupInputs);

        sortRepeatingFields();
    });

    $(document).on('click', '[data-repeating-field-delete]', function(e) {

        e.preventDefault();

        var $clicked = $(this);

        var $fieldGroup = $clicked.closest('[data-field-group]');

        $fieldGroup.remove();
    });

    $(document).ready(function() {
        $("[data-sortable]").sortable({
            containerSelector: "[data-sortable]"
            , itemSelector: "[data-sortable-item]"
            , handle: "[data-sortable-handle]"
            , placeholder: '<div class="sortable-placeholder" />'
            , onDrop: function  (item, container, _super) {
                sortRepeatingFields();
                _super(item)
            }
        });
    });
})();