<div class="panel panel-default">
<!--    <div class="panel-heading">-->
<!--        DataTables Advanced Tables-->
<!--    </div>-->
    <!-- /.panel-heading -->
    <div class="panel-body">
        <div class="table-responsive">
            <table id="itemList" class="table table-striped table-bordered table-hover dataTable no-footer">
                <thead>
                    <tr role="row">
                        <?php foreach ($listFields as $field => $data): ?>
                            <th rowspan="1" style="<?php if ($data['width']) { echo 'width: '.$data['width'].'px;'; }?>"><?php $_($data['title']); ?></th>
                        <?php endforeach; ?>
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>
        <!-- /.table-responsive -->
    </div>
    <!-- /.panel-body -->
</div>


<?php
$columns = [];
foreach ($listFields as $field => $data){
    $columns[] = [
        'className' => $data['column_classes'],
        'data' => $field,
        'dataSrc' => 'data',
        'orderable' => $data['orderable'],
        'searching' => $data['searching'],
    ];
} ?>
<script type="text/javascript">
    jQuery(function () {
        $('#itemList').dataTable({
            ajax: '/admin/<?php echo $modelName; ?>/',
            serverSide: true,
            pageLength: 25,
            columns:  JSON.parse('<?php echo json_encode($columns); ?>')
        });
    });
</script>