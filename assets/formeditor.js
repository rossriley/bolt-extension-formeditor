formeditor = {
    fieldCollectionCount: 0,

    init: function() {
        var controller = this;
        $("#form_field_add").on('click', function(){
            controller.addField();
        });

        $("#form_field_list").sortable({
            placeholder: 'field-group-placeholder'
        });

        $('.field-group select.choices-row').select2({
            tags: true,
            tokenSeparators: [',']
        });

        $('.field-group').on('click', '.form-row-delete', function(e){
            controller.deleteField($(this));
            e.preventDefault();
        });

        $('.field-group').on('click', '.expandEditor', function(e){
            controller.toggleEditor($(this));
            e.preventDefault();
        });

        $("#sidebarformdelete").on('submit', function(e){
            if(!confirm("Are you sure you want to permanently delete this form?") ) {
                e.preventDefault();
            }
        });

        $('.field-group').on('change', 'select.type-row', function(){
            controller.handleConditionals();
        });
        controller.handleConditionals();

    },

    addField: function() {
        var html = $("#form_field_list").data("prototype"),
            self = this,
            proto;

        html = html.replace(/__name__/gi, this.fieldCollectionCount++);
        proto = $(html);
        proto.find('input').removeAttr('readonly');
        proto.find('select.choices-row').select2({tags: {}, tokenSeparators: [',']});
        $("#form_field_list").append(proto);
        $("#form_field_list").sortable({
            placeholder: 'field-group-placeholder'
        });
        this.handleConditionals();
        $("#form_field_list .field-group")
            .last()
            .find('.expandEditor')
            .click(function(e) {
                self.toggleEditor($(this));
                e.preventDefault();
            });
        $(".name-row").last().focus();
    },

    deleteField: function(trigger) {
        var dodelete = confirm("Are you sure you want to remove a field?");

        if (dodelete) {
            var parent = trigger.closest('.field-group');
            var name = parent.find('input.name-row').val();
            var replace = '<input type="hidden" name="form[fields][_delete][]" value="'+name+'">';
            parent.fadeOut('fast', function(){
                parent.replaceWith(replace);
            });

        }
    },

    handleConditionals: function() {
        $("#form_field_list select.type-row").each(function(){
            varfieldtype = $(this).find("option:selected").val();
            var parent = $(this).closest('.field-group-content');
            if(varfieldtype == 'choice' ) {
                parent.find('.required-row-container').hide();
                parent.find('.choices-row-container').show();
            } else if(varfieldtype == 'text' || varfieldtype == 'textarea') {
                parent.find('.required-row-container').show();
                parent.find('.choices-row-container').hide();
            } else {
                parent.find('.required-row-container').hide();
                parent.find('.choices-row-container').hide();
            }
        });
    },

    toggleEditor: function ($obj) {
        var block = $('.' + $obj.data('id'));

        if (block.is(':visible')) {
            $obj.removeClass('visible');
            block.slideUp(350);
        } else {
            $obj.addClass('visible');
            block.slideDown(350);
        }
    }

};

jQuery(document).ready(function($) {
    formeditor.init();
});
