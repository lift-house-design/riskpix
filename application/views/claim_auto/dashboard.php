<?php /*<pre><?php var_dump($claims); ?></pre>*/?>
<?php echo html_table(
	$claims_table, 
	array('Insurer','Claim Number','Status','Name','Contact','Action'), 
	'id="claims-table"'
) ?>
<script>
$('#claims-table').dataTable({
	bLengthChange: false,
	bInfo: false,
	//bFilter: false,
	/*bPaginate: false,
	"aoColumnDefs": [
		{ "bSortable": false, aTargets: [0], sClass: 'align-center' },
		{ "bVisible": false, aTargets: [6] },
		{ "bVisible": false, aTargets: [7] },
		{ "bVisible": false, aTargets: [10] },
		{ "bVisible": false, aTargets: [11] },
		{ "bSortable": false, aTargets: [8] },
		{ "bSortable": false, aTargets: [9] }
	]*/
});

function claim_view(hash)
{
	window.location="/claim/view/"+hash;
}

function claim_edit(hash)
{
	window.location="/claim/edit/"+hash;
}

function claim_remind(button, hash)
{
	$('button.email').attr('disabled','disabled');
	var done = 0;
	$.get(
		'/claim/text_message/'+hash,
		{},
		function(data)
		{
			if(done++)
			{
				$(button).attr('disabled','disabled');
				$(button).val('Sent!');
			}
		},
		'json'
	);
	$.get(
		'/claim/email_message/'+hash,
		{},
		function(data)
		{
			if(done++)
			{
				$(button).attr('disabled','disabled');
				$(button).val('Sent!');
			}
		},
		'json'
	);
}

function claim_remind_email(button, hash)
{
	$('button.email').attr('disabled','disabled');
	$.get(
		'/claim/email_message/'+hash,
		{},
		function(data)
		{
			$(button).attr('disabled','disabled');
			$(button).val('Sent!');
		},
		'json'
	);
}

function claim_remind_text(button, hash)
{
	$('button.email').attr('disabled','disabled');
	$.get(
		'/claim/text_message/'+hash,
		{},
		function(data)
		{
			$(button).attr('disabled','disabled');
			$(button).val('Sent!');
		},
		'json'
	);
}

</script>