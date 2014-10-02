<?php /*<pre><?php var_dump($claims); ?></pre>*/?>
<h2>Your Reports</h2>
<?php echo html_table(
	$claims_table, 
	array('Sortable Date','Date','Policy Quote Number','Status','Action'), 
	'id="claims-table"'
) ?>
<script>
$('#claims-table').dataTable({
	bLengthChange: false,
	bInfo: false,
	//bFilter: false,
	bPaginate: false,
	aaSorting : [[0, 'desc']],
	"aoColumnDefs": [
        {"bVisible": false, aTargets: [0]},
		{"iDataSort": 0, aTargets: [1]}
		/*{ "bSortable": false, aTargets: [0], sClass: 'align-center' },
		{ "bVisible": false, aTargets: [6] },
		{ "bVisible": false, aTargets: [7] },
		{ "bVisible": false, aTargets: [10] },
		{ "bVisible": false, aTargets: [11] },
		{ "bSortable": false, aTargets: [8] },
		{ "bSortable": false, aTargets: [9] }*/
	]
});

function claim_view(hash)
{
	window.location="/dispatcher/view/"+hash;
}
</script>