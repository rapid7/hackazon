<?php
if (!isset($gwtphpmap)) $gwtphpmap = array();
$gwtphpmap[] = 
	array(
	'className' => 'com.ntobjectives.hackazon.helpdesk.client.HelpdeskService',
	'mappedBy' => 'com.ntobjectives.hackazon.helpdesk.client.HelpdeskService',
	'methods' => array (
		array(
			'name' => 'getDate',
			'mappedName' => 'getDate',
			'returnType' => 'java.util.Date',
			'returnTypeCRC' => '3385151746',
			'params' => array(
			) ,
			'throws' => array(
			)
		),
		array(
			'name' => 'getAppData',
			'mappedName' => 'getAppData',
			'returnType' => 'com.ntobjectives.hackazon.helpdesk.client.entity.ApplicationData',
			'returnTypeCRC' => '3860996832',
			'params' => array(
			) ,
			'throws' => array(
			)
		),
		array(
			'name' => 'getMessage',
			'mappedName' => 'getMessage',
			'returnType' => 'java.lang.String',
			'returnTypeCRC' => '2004016611',
			'params' => array(
				array('type' => 'java.lang.String'),
			) ,
			'throws' => array(
			)
		),
		array(
			'name' => 'getEnquiryById',
			'mappedName' => 'getEnquiryById',
			'returnType' => 'com.ntobjectives.hackazon.helpdesk.client.entity.Enquiry',
			'returnTypeCRC' => '1412794674',
			'params' => array(
				array('type' => 'I'),
			) ,
			'throws' => array(
			)
		),
		array(
			'name' => 'getEnquiries',
			'mappedName' => 'getEnquiries',
			'returnType' => 'java.util.ArrayList<com.ntobjectives.hackazon.helpdesk.client.entity.Enquiry>',
			'returnTypeCRC' => '4159755760<1412794674>',
			'params' => array(
			) ,
			'throws' => array(
			)
		),
		array(
			'name' => 'createEnquiryMessage',
			'mappedName' => 'createEnquiryMessage',
			'returnType' => 'com.ntobjectives.hackazon.helpdesk.client.entity.EnquiryMessage',
			'returnTypeCRC' => '689768322',
			'params' => array(
				array('type' => 'com.ntobjectives.hackazon.helpdesk.client.entity.EnquiryMessage'),
			) ,
			'throws' => array(
				array('type' => 'java.lang.IllegalArgumentException'),
			)
		),
		array(
			'name' => 'getEnquiriesPagePerPage',
			'mappedName' => 'getEnquiriesPagePerPage',
			'returnType' => 'java.util.ArrayList<com.ntobjectives.hackazon.helpdesk.client.entity.Enquiry>',
			'returnTypeCRC' => '4159755760<1412794674>',
			'params' => array(
				array('type' => 'I'),
				array('type' => 'I'),
			) ,
			'throws' => array(
			)
		),
		array(
			'name' => 'getEnquiriesPage',
			'mappedName' => 'getEnquiriesPage',
			'returnType' => 'java.util.ArrayList<com.ntobjectives.hackazon.helpdesk.client.entity.Enquiry>',
			'returnTypeCRC' => '4159755760<1412794674>',
			'params' => array(
				array('type' => 'I'),
			) ,
			'throws' => array(
			)
		),
		array(
			'name' => 'createEnquiry',
			'mappedName' => 'createEnquiry',
			'returnType' => 'com.ntobjectives.hackazon.helpdesk.client.entity.Enquiry',
			'returnTypeCRC' => '1412794674',
			'params' => array(
				array('type' => 'com.ntobjectives.hackazon.helpdesk.client.entity.Enquiry'),
			) ,
			'throws' => array(
				array('type' => 'java.lang.IllegalArgumentException'),
			)
		),
		array(
			'name' => 'isUserAuthenticated',
			'mappedName' => 'isUserAuthenticated',
			'returnType' => 'java.lang.Boolean',
			'returnTypeCRC' => '476441737',
			'params' => array(
			) ,
			'throws' => array(
			)
		),
		array(
			'name' => 'getEnquiryMessages',
			'mappedName' => 'getEnquiryMessages',
			'returnType' => 'java.util.ArrayList<com.ntobjectives.hackazon.helpdesk.client.entity.EnquiryMessage>',
			'returnTypeCRC' => '4159755760<689768322>',
			'params' => array(
				array('type' => 'I'),
			) ,
			'throws' => array(
				array('type' => 'java.lang.IllegalArgumentException'),
			)
		)
	),
);
