@extends('layouts.app')

@section('title', '| Cadastro de Materiais')

@section('content')
<link  href="https://cdn.datatables.net/1.10.16/css/jquery.dataTables.min.css" rel="stylesheet">
<div class="col-lg-10 col-lg-offset-1">
    <h1><i class="fa fa-users"></i>Materiais SAP</h1>
    <br />
    <div align="right">
      <button type="button" name="add" id="add_data" class="btn btn-success btn-sm">Novo</button>
    </div>
    <hr>
    <div class="table-responsive">
        <table id="cadastro_materiais_table" class="table table-bordered table-striped">

            <thead>
                <tr>
                    <th>Material</th>
                    <th>Descrição</th>
                    <th>Tipo</th>
                    <th>Data criação</th>
                    <th>Data ultima atualização</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tfoot>
                <tr>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                </tr>
            </tfoot>
        </table>
    </div>
</div>

<div id="cadastroMaterialModal" class="modal fade" role="dialog">
  <div class="modal-dialog">
      <div class="modal-content">
        <form method="post" id="cadastroMaterialForm">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal">&times;</button>
            <h4 class="modal-title">Cadastrar material</h4>
          </div>
          <div class="modal-body">
            {{csrf_field()}}
            <span id="form_output"></span>
            <div class="form-group">
              <label>Codigo Material</label>
              <input type="text" name="codigo_material" id="codigo_material" class="form-control"/>
            </div>
            <div class="form-group">
              <label>Descrição Material</label>
              <input type="text" name="descricao_material" id="descricao_material" class="form-control"/>
            </div>
            <div class="form-group">
              <label>Tipo</label>
              <input type="text" name="tipo" id="tipo" class="form-control"/>
            </div>
          </div>
          <div class="modal-footer">
            <input type="hidden" name="material_id" id="material_id" value="" />
            <input type="hidden" name="button_action" id="button_action" value="insert" />
            <input type="submit" name="submit" id="action" value="Cadastrar" class="btn btn-info" />
            <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
          </div>
        </form>
      </div>
  </div>
</div>

<div id="deleteMaterialModal" class="modal fade" role="dialog">
  <div class="modal-dialog">
      <div class="modal-content">
        <form method="post" id="deleteMaterialForm">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal">&times;</button>
            <h4 class="modal-title">Confirmar</h4>
          </div>
          <div class="modal-body">
            {{csrf_field()}}
            <p>Deseja deletar o material selecionado?</p>
          </div>
          <div class="modal-footer">
            <input type="hidden" name="material_delete_id" id="material_delete_id" value="" />
            <input type="submit" name="submit" id="delete" value="Deletar" class="btn btn-danger" />
            <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
          </div>
        </form>
      </div>
    </div>
  </div>


<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.0/jquery.min.js"></script>
<script src="https://cdn.datatables.net/1.10.16/js/jquery.dataTables.min.js" defer></script>
  
<script>;
$(document).ready(function(){

  $('#add_data').click(function(){
    $('#cadastroMaterialModal').modal('show');
    $('#cadastroMaterialForm')[0].reset();
    $('#form_output').html('');
    $('#button_action').val('insert');
    $('#action').val('Cadastrar');
  });

  $('#cadastroMaterialForm').on('submit', function(event){
    event.preventDefault();
    var form_data = $(this).serialize();
    $.ajax({
      url:"{{route('materiais.cadastro_materiais.store')}}",
      method:'post',
      data:form_data,
      dataType:'json',
      success: function(data){
        if(data.error.length >0){
          var error_html ='';
          for(var count = 0; count < data.error.length; count++){
            error_html += '<div class="alert alert-danger">'+data.error[count]+ '</div>';
          } 
          $('#form_output').html(error_html);
        }else{

          if($('#button_action').val() == 'update'){
            $('#cadastroMaterialForm')[0].reset();
            $('#form_output').html('');
            $('#cadastroMaterialModal').modal('hide');
            $('#cadastro_materiais_table').DataTable().ajax.reload();
          }else{
            $('#form_output').html(data).success;
            $('#cadastroMaterialForm')[0].reset();
            $('#action').val('Cadastrar');
            $('#modal-header').text('Cadastrar material');
            $('#cadastro_materiais_table').DataTable().ajax.reload();
          }
          
        }

      }
    });
  });

  $(document).on('click', '.edit', function(){
    var id = $(this).attr('id');
    $.ajax({
      url:"{{route('materiais.cadastro_materiais.edit')}}",
      method:'get',
      data:{id:id},
      dataType:'json',
      success:function(data){
        $('#codigo_material').val(data.codigo_material);
        $('#descricao_material').val(data.descricao_material);
        $('#tipo').val(data.tipo);
        $('#material_id').val(id);
        $('#cadastroMaterialModal').modal('show');
        $('#action').val('Editar');
        $('.modal-title').text('Editar material');
        $('#button_action').val('update');
        
      } 
    });
  });

  $(document).on('click', '.delete', function(){

    var id = $(this).attr('id');
    $('#material_delete_id').val(id);
    $('#deleteMaterialModal').modal('show');
    $('#delete').attr("disabled", false);
    
  });

  $('#deleteMaterialForm').on('submit', function(event){
    $('#delete').attr("disabled", true);
    event.preventDefault();
    var form_data = $(this).serialize();
    $.ajax({
        url: "{{route('materiais.cadastro_materiais.delete')}}",
        type: 'DELETE',
        dataType: 'json',
        data:form_data,
        method: 'post',
        success:function (data) {
          $('#deleteMaterialModal').modal('hide');
          $('#cadastro_materiais_table').DataTable().draw(false);
        }
    });
  });


  var table = $('#cadastro_materiais_table').DataTable({
                "resposive":true,
                "orderCellsTop": true,
                "fixedHeader": true,
               "processing": true,
               "serverSide": true,
               "ajax": '{{ route("materiais.cadastro_materiais.getData") }}',
               "columns": [
                        { data: 'codigo_material', name: 'codigo_material' },
                        { data: 'descricao_material', name: 'descricao_material' },
                        { data: 'tipo', name: 'tipo' },
                        { data: 'created_at', name: 'created_at' },
                        { data: 'updated_at', name: 'updated_at' },
                        { data: 'action', name: 'action', orderable:false, searchable:false},
                       
                     ],
                initComplete: function (settings, json) {
                this.api().columns([0,1,2]).every( function () {
                    var column = this;
                    var select = $('<select><option value=""></option></select>')
                        .appendTo( $(column.footer()).empty() )
                        .on( 'change', function () {
                            var val = $.fn.dataTable.util.escapeRegex(
                                $(this).val()
                            );
     
                            column
                                .search( val ? '^'+val+'$' : '', true, false )
                                .draw();
                        } );
                    
                     // Capture the data from the JSON to populate the select boxes with all the options
                      var extraData = (function(i) {
                        switch(i) {
                        case 0:
                          return json.allCodigoMaterial;
                        case 1:
                          return json.allDescricaoMaterial;
                        case 2:
                          return json.allTipo;
                        }

                      })(column.index());
                         // Draw select options
                        extraData.sort().forEach( function ( d ) {
                          if(column.search() === d){
                            select.append( '<option value="'+d+'" selected="selected">'+d+'</option>' )
                          } else {
                            select.append( '<option value="'+d+'">'+d+'</option>' )
                          }
                        } );
                } );
            }
               
  });
});

</script>
@endsection