<? include_once($_SERVER['DOCUMENT_ROOT'] . '/xtracks-app/bootstrap.php'); 

AUTH::require_user();


//show real or filtered clicks
	$mysql['user_id'] = mysql_real_escape_string($_SESSION['user_id']);
	$user_sql = "SELECT user_pref_breakdown, user_pref_show, user_cpc_or_cpv FROM 202_users_pref WHERE user_id=".$mysql['user_id'];
	$user_result = _mysql_query($user_sql, $dbGlobalLink); //($user_sql);
	$user_row = mysql_fetch_assoc($user_result);	
	$breakdown = $user_row['user_pref_breakdown'];

	if ($user_row['user_cpc_or_cpv'] == 'cpv')  $cpv = true;
	else 										$cpv = false;


//keywords already set in the table, just just download them
	if (empty($mysql['order'])) { 
		$mysql['order'] = ' ORDER BY sort_keyword_clicks DESC';   
	}
	$db_table = '202_sort_keywords';
							 
	$query = query('SELECT * FROM 202_sort_keywords LEFT JOIN 202_keywords USING (keyword_id)', $db_table, false, false, false,  $mysql['order'],false, false, true);
	$keyword_sql = $query['click_sql'];
	$keyword_result = mysql_query($keyword_sql) or record_mysql_error($keyword_sql);  
	

header("Content-type: application/octet-stream");

# replace excelfile.xls with whatever you want the filename to default to
header("Content-Disposition: attachment; filename=XT_keywords_".time().".xls");
header("Pragma: no-cache");
header("Expires: 0");
	
	
echo "Keyword" . "\t" . "Clicks" . "\t" . "Leads" . "\t" . "S/U"  . "\t" . "Payout"  . "\t" . "EPC"  . "\t" . "Avg CPC"  . "\t" . "Income"  . "\t" . "Cost"  . "\t" . "Net" . "\t" . "ROI"  . "\n";


while ($keyword_row = mysql_fetch_array($keyword_result, MYSQL_ASSOC)) { 
	
	if (!$keyword_row['keyword']) { 
		$keyword_row['keyword'] = '[no keyword]';    
	} 

	echo 
	$keyword_row['keyword'] . "\t" . 
	$keyword_row['sort_keyword_clicks'] . "\t" .
	$keyword_row['sort_keyword_leads'] . "\t" .
	$keyword_row['sort_keyword_su_ratio'].'%' . "\t" .
	dollar_format($keyword_row['sort_keyword_payout']) . "\t" .
	dollar_format($keyword_row['sort_keyword_epc']) . "\t" .
	dollar_format($keyword_row['sort_keyword_avg_cpc'], $cpv) . "\t" .
	dollar_format($keyword_row['sort_keyword_income']) . "\t" .
	dollar_format($keyword_row['sort_keyword_cost'], $cpv) . "\t" .
	dollar_format($keyword_row['sort_keyword_net'], $cpv) . "\t" .
	$keyword_row['sort_keyword_roi'].'%' . "\n";
	
}
