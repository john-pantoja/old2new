<?php
	
	header('Content-type: text/plain');
	error_reporting(E_ALL);
	ini_set('display_errors', true);
	
	 $newServcies = array(
        'affiliatePrograms', 'affiliates','affiliates', 'contacts', 'data', 'discounts',
        'emails', 'files', 'funnels', 'invoices', 'orders', 'products',
        'search', 'shipping', 'webForms','webTracking'
    );
    
     $oldServices = array(
        'AffiliateProgramService', 'APIAffiliateService','AffiliateService', 'ContactService', 'DataService', 'DiscountService',
        'APIEmailService', 'FileService', 'FunnelService', 'InvoiceService', 'OrderService', 'ProductService',
        'SearchService', 'ShippingService', 'WebFormService','WebTrackingService'
    );
    
    $excludeMethods = array(
		'APIEmailService' => array(
			'sendEmail','addEmailTemplate','optStatus'
		),
		'ContactService' => array(
			'addToGroup','removeFromGroup','addToCampaign','pauseCampaign','removeFromCampaign','runActionSequence'
	    ),
	    'DataService' => array(
		    'find','query','update','add','getTemporaryKey','echo','getAppSetting','findByField'
	    ),
	    'FileService' => array(
		    'uploadFile'
	    ),
	    'CreditCardSubmissionService' => array(
		    'requestSubmissionToken','requestCreditCardId'
	    ),
	    'InvoiceService' => array(
		    'addManualPayment','addOrderCommissionOverride','addPaymentPlan','addRecurringOrder','addRecurringOrder','calculateAmountOwed','createBlankOrder','createInvoiceForRecurring','locateExistingCard','validateCreditCard','updateJobRecurringNextBillDate'
	    )
    );
    
    $excludedServcies = array(
    );
    
    $sigs = array(
    	'signatures' => array(),
    	'replacements' => array()
    );
    
    if (file_exists('signatures_pre.json')) {
	    $pre = json_decode(file_get_contents('signatures_pre.json'), true);
    }
    
    if (file_exists('signatures_post.json')) {
	     $post = json_decode(file_get_contents('signatures_post.json'), true);
    }
	
	if (isset($pre, $pre['signatures'], $pre['replacements'])) {
		foreach ($pre['signatures'] as $k => $v) {
			$sigs['signatures'][$k] = $v;
		  
			if (isset($pre['replacements'][$k])) {
				$sigs['replacements'][$k] = $pre['replacements'][$k];
			}
		}
	}
	
	$sigs['signatures']['methods'] = array();
	$sigs['replacements']['methods'] = array();
	
/* methods with issues */

	$sigs['signatures']['methods'][] = '/[a-z0-9_$]+->vendorCon\(([a-z0-9\"\'_$]+) *?, *?([a-z0-9$\'\"_]+) *?, *?([a-z0-9_\'\"$\+]+) *?,? *?([\w\d\W_$]+)? *?,? *?([\w\d\W_$]+)? *?\)\;?/Ui';
	$sigs['replacements']['methods'][] = 'is_object(@SDK_APP_VAR@)';
	
	$sigs['signatures']['methods'][] = '/[a-z0-9_$]+->cfgCon\(([a-z0-9\"\'_$-]+) *?,? *?([a-z0-9\"\'_$-]+)? *?,? *?([a-z0-9\"\'_$-]+)? *?\)/Ui';
	$sigs['replacements']['methods'][] = 'is_object(@SDK_APP_VAR@)';
	
	$sigs['signatures']['methods'][] = '/\-\>dsQuery\(([a-z0-9\"\'_$]+) *?, *?([a-z0-9$\'\"_]+) *?, *?([a-z0-9_\'\"$\+]+) *?, *?([\w\d\W_$]+) *?, *?([\w\d\W_$]+) *?\)/Ui';
	$sigs['replacements']['methods'][] = '->data()->query($1, $2, $3, $4, $5, \'Id\', true)';
	
	$sigs['signatures']['methods'][] = '/\-\>dsQueryOrderBy\(([a-z0-9\"\'_$]+) *?, *?([a-z0-9$\'\"_]+) *?, *?([a-z0-9_\'\"$\+]+) *?, *?([\w\d\W_$]+) *?, *?([\w\d\W_$]+) *?, *?([\w\d\W_$]+) *?\)/Ui';
	$sigs['replacements']['methods'][] = '->data()->query($1, $2, $3, $4, $5, $6, true)';
	
	$sigs['signatures']['methods'][] = '/\-\>dsQueryOrderBy\(([a-z0-9\"\'_$]+) *?, *?([a-z0-9$\'\"_]+) *?, *?([a-z0-9_\'\"$\+]+) *?, *?([\w\d\W_$]+) *?, *?([\w\d\W_$]+) *?, *?([\w\d\W_$]+) *?, *?([\w\d\W_$]+) *?\)/Ui';
	$sigs['replacements']['methods'][] = '->data()->query($1, $2, $3, $4, $5, $6, $7)';
	
	$sigs['signatures']['methods'][] = '/->dsAddWithImage/Ui';
	$sigs['replacements']['methods'][] = '->data()->add';
	
	$sigs['signatures']['methods'][] = '/->dsAdd/Ui';
	$sigs['replacements']['methods'][] = '->data()->add';
	
	$sigs['signatures']['methods'][] = '/->dsUpdateWithImage/Ui';
	$sigs['replacements']['methods'][] = '->data()->update';
	
	$sigs['signatures']['methods'][] = '/->dsUpdate/Ui';
	$sigs['replacements']['methods'][] = '->data()->update';
	
	$sigs['signatures']['methods'][] = '/->dsGetSetting/Ui';
	$sigs['replacements']['methods'][] = '->data()->getAppSetting';
	
	$sigs['signatures']['methods'][] = '/[a-z0-9_$]+->appEcho\(*?\)\;?/Ui';
	$sigs['replacements']['methods'][] = 'is_object(@SDK_APP_VAR@)';
	
	$sigs['signatures']['methods'][] = '/\-\>dsFind/i';
	$sigs['replacements']['methods'][] = '->data()->findByField';	

	$sigs['signatures']['methods'][] = '/\-\>grpAssign/i';
	$sigs['replacements']['methods'][] = '->contacts()->addToGroup';
	
	$sigs['signatures']['methods'][] = '/\-\>grpRemove/i';
	$sigs['replacements']['methods'][] = '->contacts()->removeFromGroup';
	
	$sigs['signatures']['methods'][] = '/\-\>campAssign/i';
	$sigs['replacements']['methods'][] = '->contacts()->addToCampaign';
	
	$sigs['signatures']['methods'][] = '/\-\>campPause/i';
	$sigs['replacements']['methods'][] = '->contacts()->pauseCampaign';
		
	$sigs['signatures']['methods'][] = '/\-\>campRemove/i';
	$sigs['replacements']['methods'][] = '->contacts()->removeFromCampaign';
	
	$sigs['signatures']['methods'][] = '/\-\>runAS/i';
	$sigs['replacements']['methods'][] = '->contacts()->runActionSequence';
	
	$sigs['signatures']['methods'][] = '/\-\>sendEmail/i';
	$sigs['replacements']['methods'][] = '->emails()->sendEmail';
	
	$sigs['signatures']['methods'][] = '/\-\>sendTemplate/i';
	$sigs['replacements']['methods'][] = '->emails()->sendEmail';
	
	$sigs['signatures']['methods'][] = '/\-\>optStatus/i';
	$sigs['replacements']['methods'][] = '->emails()->getOptStatus';
	
	$sigs['signatures']['methods'][] = '/\-\>createEmailTemplate/i';
	$sigs['replacements']['methods'][] = '->emails()->addEmailTemplate';
	
	$sigs['signatures']['methods'][] = '/\-\>addEmailTemplate/i';
	$sigs['replacements']['methods'][] = '->emails()->addEmailTemplate';
	
	$sigs['signatures']['methods'][] = '/\$([a-z0-9 _]+)\-\>uploadFile\( *?([\w\d\W_$=]+) *?, *?([\w\d\W_$ =]+), *?([\w\d\W_$]+) *?\)/i';
	$sigs['replacements']['methods'][] = '\$$1->files()->uploadFile($2,$3,$4)';
	
	$sigs['signatures']['methods'][] = '/\$([a-z0-9 _]+)\-\>uploadFile\( *?([\w\d\W_$=]+) *?, *?([\w\d\W_$ =]+) *?\)/i';
	$sigs['replacements']['methods'][] = '\$$1->files()->uploadFile($1,$2)';
	
	$sigs['signatures']['methods'][] = '/[a-z0-9_$]+->enableLogging\([0,1]?\)\;?/Ui';
	$sigs['replacements']['methods'][] = '//';
	
	$sigs['signatures']['methods'][] = '/\-\>requestCcSubmissionToken/i';
	$sigs['replacements']['methods'][] = ';// does not appear in new SDK requestSubmissionToken';
	
	$sigs['signatures']['methods'][] = '/\-\>requestCreditCardId/i';
	$sigs['replacements']['methods'][] = ';//does not appear in new SDK requestCreditCardId';
	
	$sigs['signatures']['methods'][] = '/\-\>manualPmt/i';
	$sigs['replacements']['methods'][] = '->invoices()->addManualPayment';
	
	$sigs['signatures']['methods'][] = '/\-\>commOverride/i';
	$sigs['replacements']['methods'][] = '->invoices()->addOrderCommissionOverride';
	
	$sigs['signatures']['methods'][] = '/\-\>payPlan/i';
	$sigs['replacements']['methods'][] = '->invoices()->addPaymentPlan';
	
	$sigs['signatures']['methods'][] = '/\-\>addRecurring/i';
	$sigs['replacements']['methods'][] = '->invoices()->addRecurringOrder';
	
	$sigs['signatures']['methods'][] = '/\-\>addRecurringAdv/i';
	$sigs['replacements']['methods'][] = '->invoices()->addRecurringOrder';
	
	$sigs['signatures']['methods'][] = '/\-\>amtOwed/i';
	$sigs['replacements']['methods'][] = '->invoices()->calculateAmountOwed';
	
	$sigs['signatures']['methods'][] = '/\-\>blankOrder/i';
	$sigs['replacements']['methods'][] = '->invoices()->createBlankOrder';
	
	$sigs['signatures']['methods'][] = '/\-\>recurringInvoice/i';
	$sigs['replacements']['methods'][] = '->invoices()->createInvoiceForRecurring';
	
	$sigs['signatures']['methods'][] = '/\-\>locateCard/i';
	$sigs['replacements']['methods'][] = '->invoices()->locateExistingCard';
	
	$sigs['signatures']['methods'][] = '/\-\>validateCard/i';
	$sigs['replacements']['methods'][] = '->invoices()->validateCreditCard';
	
	$sigs['signatures']['methods'][] = '/\-\>updateSubscriptionNextBillDate/i';
	$sigs['replacements']['methods'][] = '->invoices()->updateJobRecurringNextBillDate';
	
	
	$sdk = file_get_contents('oldsdk.php');
	
	preg_match_all('/"([a-z]+)\.([a-z]+)/i', $sdk, $matches);

	foreach ($matches[0] as $k => $match) {
		
		$service = $matches[1][$k];
		$method = $matches[2][$k];
		
		if (!in_array($service, $excludedServcies) && (!isset($excludeMethods[$service]) || !in_array($method, $excludeMethods[$service]))) {
			
			switch (true) {
				case $service == 'ContactService':
					switch (true) {
						case stripos($sdk, $method . 'Con') !== false:
							$sigs['signatures']['methods'][] = '/\-\>' . $method . 'Con' . '/i';
							$sigs['replacements']['methods'][] = '->contacts()->' . lcfirst(substr($method, 0));
						break;
						
						default:
							$sigs['signatures']['methods'][] = '/\-\>' . $method . '/i';
							$sigs['replacements']['methods'][] = '->contacts()->' . $method;
						break;
					}
				break;
				
				case $service == 'DataService':
				
					switch (true) {
						case stripos($sdk, 'ds' . $method) !== false:
							$sigs['signatures']['methods'][] = '/\-\>' . 'ds' . ucfirst($method) . '/i';
							$sigs['replacements']['methods'][] = '->data()->' . $method;
						break;
						
						default:
							$sigs['signatures']['methods'][] = '/\-\>' . $method . '/i';
							$sigs['replacements']['methods'][] = '->data()->' . $method;
						break;
					}
				break;
				
				default:
					$oldKey = array_search($service, $oldServices);
				
					$sigs['signatures']['methods'][] = '/\-\>' . $method . '/i';
					
					if ($oldKey !== false) {
						$sigs['replacements']['methods'][] = '->' . $newServcies[$oldKey] . '()->' . $method;
					}
					
					else {
						$sigs['replacements']['methods'][] = ';//does not appear in new SDK ' . $service . ' ' . $method;
					}
				break;
			}
		}
	}
	
	if (isset($post, $post['signatures'], $post['replacements'])) {
		foreach ($post['signatures'] as $k => $v) {
			$sigs['signatures'][$k] = $v;
		  
			if (isset($post['replacements'][$k])) {
				$sigs['replacements'][$k] = $post['replacements'][$k];
			}
		}
	}
	
	var_dump($sigs);
	
	file_put_contents('signatures.json', json_encode($sigs));
	
	
?>