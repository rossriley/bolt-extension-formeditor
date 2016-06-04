Bolt.formeditor = {
    
    fieldCollectionCount: 0,
    
    init: function() {
        var controller = this;
        $("#form_field_add").on('click', function(){
            controller.addField();
        });
        
        $("#form_fields").sortable();
        $('#form_fields select.choices-row').select2({tags: true, tokenSeparators: [',']});
        
        $('#form_fields').on('click', '.delete-icon .form-row-delete', function(e){ 
            controller.deleteField($(this));      
            e.preventDefault();
        });
        
        $("#sidebarformdelete").on('submit', function(e){
            if(!confirm("Are you sure you want to permanently delete this form?") ) {
                e.preventDefault();
            }
        }); 
        
        $('#form_fields').on('change', 'select.type-row', function(){
            controller.handleConditionals();
        }); 
        controller.handleConditionals(); 
        
    },
    
    addField: function() {
        var html = $("#form_fields").data("prototype");
        html = html.replace(/__name__/gi, this.fieldCollectionCount++);
        var proto = $(html);
        proto.find('input').removeAttr('readonly');
        proto.find('select.choices-row').select2({tags: {}, tokenSeparators: [',']});
        $("#form_fields").append(proto); 
        $("#form_fields").sortable();
        this.handleConditionals();
        $(".name-row").last().focus();
    },
    
    deleteField: function(trigger) {
        var dodelete = confirm("Are you sure you want to remove a field?");
        
        if (dodelete) {
            var parent = trigger.closest('.form-field-row');
            var name = parent.find('input.name-row').val();
            var replace = '<input type="hidden" name="form[fields][_delete][]" value="'+name+'">';
            parent.fadeOut('fast', function(){
                parent.replaceWith(replace);
            });
            
        }
    },
    
    handleConditionals: function() {
        $("#form_fields select.type-row").each(function(){
            varfieldtype = $(this).find("option:selected").val();
            var parent = $(this).closest('.outer-row');
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
    }
    
}



jQuery(document).ready(function($) {
    Bolt.formeditor.init(); 
});