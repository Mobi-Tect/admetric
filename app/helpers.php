<?php
//require_once dirname(__FILE__). 'libraries/g/examples/AdWords/v201601/init.php';
//require_once dirname(__FILE__). 'libraries/g/src/Google/Api/Ads/AdWords/v201601/AdGroupService.php';
  //echo ADWORDS_UTIL_VERSION_PATH;exit;
require_once dirname(__FILE__). '/g/examples/AdWords/v201601/init.php';
require_once dirname(__FILE__). '/g/src/Google/Api/Ads/Common/Util/ChoiceUtils.php';
require_once dirname(__FILE__). '/g/src/Google/Api/Ads/Common/Util/OgnlUtils.php';
require_once dirname(__FILE__).'/g/src/Google/Api/Ads/AdWords/Util/v201601/ReportClasses.php';
require_once dirname(__FILE__).'/g/src/Google/Api/Ads/AdWords/Util/v201601/ReportUtils.php';

 define('oauth2Info_in_session','oauth2Info'); 

  define('USER_AGENT','Admetric'); 

 ini_set('max_execution_time', '-1');
 define('DEVELOPER_TOKEN','2DuyW5Ec19q8U2wFiT_pJw'); 

define('oauth2_clientId','947744393545-er9au06qtncan9nlkbdjt14sgu007vi0.apps.googleusercontent.com'); 

define('oauth2_clientSecret','aDLsbyvAofRObJl8NVtNZ7y1'); 



define('oauth2_clientId_new','947744393545-er9au06qtncan9nlkbdjt14sgu007vi0.apps.googleusercontent.com');                             

define('oauth2_clientSecret_new','aDLsbyvAofRObJl8NVtNZ7y1');
define('SpecSeparatorStr', ',}/$^&');

define('SpecSeparatorStr2', 'Uj4M2dF');


  
  function PrevInitAdwordsUserSettings(&$user)                                            
  {  
  
     $user = new AdWordsUser();
    //echo '<pre>';print_r($user);echo '</pre>';
     $user->SetEmail(null); 
  
     
  
     $user->SetPassword(null);                 
  
     $user->SetUserAgent(USER_AGENT); 
	 
	 $user->SetClientLibraryUserAgent(USER_AGENT); 
  
     $user->SetDeveloperToken(DEVELOPER_TOKEN);   
  
     $user->SetOAuth2Info( GetOAuth2InfoArray() );         
    //echo $user->GetDeveloperToken();
     $IsSandBoxMode = strpos($user->GetDeveloperToken(), "++USD") !== false && strpos($user->GetDeveloperToken(), "@") !== false; 
  
    $user->SetDefaultServer($IsSandBoxMode ? "https://adwords-sandbox.google.com" : "https://adwords.google.com");      
    //echo '<pre>';print_r($user);echo '</pre>';
  }//function PrevInitAdwordsUserSettings(&$user)
  
  
  function GetOAuth2InfoArray()

{

    //$CurUseMewToken = isset($GLOBALS["UseNewDevToken"]) ? $GLOBALS["UseNewDevToken"] : false;
  $CurUseMewToken = 0;

    $CurOAuth2ClientId = $CurUseMewToken ? oauth2_clientId_new : oauth2_clientId;

    $CurOAuth2ClientSecret = $CurUseMewToken ? oauth2_clientSecret_new : oauth2_clientSecret;

    $result =  array('client_id' => $CurOAuth2ClientId, 'client_secret' => $CurOAuth2ClientSecret);
  //echo '<pre>';print_r($result);echo '------------</pre>';
  return $result;

}//function GetOAuth2InfoArray()


function SaveAssoteatedArrayToOneCSVStr($AssoteatedArray)

{

    $OneCSVStr = "";     

    foreach ($AssoteatedArray as $Cur_key => $Cur_value)      

       $OneCSVStr .= (strlen($OneCSVStr) > 0 ? SpecSeparatorStr : "").$Cur_key.SpecSeparatorStr.$Cur_value;

    return $OneCSVStr;

}//function SaveAssoteatedArrayToOneCSVStr(&$AssoteatedArray)


function LoadAdWordsAccessTokenForClient(AdWordsUser &$user, $ClientID , $SelChildAccountID = null)                                                                              

{

    
  
    $IsAccessToken = $ClientID;
  
    if($IsAccessToken)

    {

        

        $user->SetOAuth2Info( LoadAssoteatedArrayFromOneCSVStr($ClientID) );        
    
        $user->SetClientCustomerId( !empty($AdWordsAccessTokensFromDB['ChildAccountId']) ? $AdWordsAccessTokensFromDB['ChildAccountId'] : null );             

    }//if($IsAccessToken && !empty($AdWordsAccessTokensFromDB[0][1]))

    else

    {

        $user->SetOAuth2Info(GetOAuth2InfoArray());        

        $user->SetClientCustomerId(null);   

    }//else from if($IsAccessToken && !empty($AdWordsAccessTokensFromDB[0][1]))       


    return $IsAccessToken;

}//function LoadAdWordsAccessToken(AdWordsUser &$user, $ClientID)


function GetChildAccountsListAsAr(&$user, &$MCCAccountName)

{ 

    $ChildAccountsListA = array(); //define a string variable to store child accounts list in CSV format    
 
    $ChildAccountsList_from_CustomerService = GetChildAccountsList_from_CustomerService($user); //download child accounts list from 'CustomerService' of AdWords API 

    $IsMCCAccount = $ChildAccountsList_from_CustomerService[2];  //check if account is MCC

    $MCCAccountName = $ChildAccountsList_from_CustomerService[0];

    

    if($IsMCCAccount) //if account is MCC

    {

        $childAccountsList = GetChildAccountsList($user); //download child accounts list from 'ManagedCustomerService' of AdWords API and store result in $childAccountsList variable

        foreach($childAccountsList as $childAccount)  //cycle for each child account from $childAccountsList array

          if(!$childAccount->canManageClients) //check if account not have childrens

              array_push($ChildAccountsListA, array($childAccount->customerId, $childAccount->name /*.' ('.$childAccount->login.')'*/));                      

    }//if($IsMCCAccount)

    else //if account is non MCC (single)

       array_push($ChildAccountsListA, array($ChildAccountsList_from_CustomerService[1], $ChildAccountsList_from_CustomerService[0]));                      

    return $ChildAccountsListA; //return string variable with child accounts list in CSV format

}//function GetChildAccountsListAsAr(&$user, &$MCCAccountName)



function LoadAssoteatedArrayFromOneCSVStr($OneCSVStr)
{

    $AssoteatedArray = array();    

    $ParsedStrArray = explode(SpecSeparatorStr, $OneCSVStr);    

    for($i = 0; $i+1 < count($ParsedStrArray); $i+=2)      

         $AssoteatedArray[$ParsedStrArray[$i]] = $ParsedStrArray[$i+1];    

    return $AssoteatedArray;

}//function LoadAssoteatedArrayFromOneCSVStr(&$OneCSVStr)

function GetChildAccountsList_from_CustomerService(&$user)
{

    $current = time();
	$CurClientID = $user->GetClientCustomerId(); 

    $user->SetClientCustomerId(null);   
	
	$user->SetUserAgent(USER_AGENT); 
	
    $customerService = $user->GetService('CustomerService', ADWORDS_VERSION); 
	
    $selector = new Selector(); 

    $selector->fields = array('customerId');         

    $page = $customerService->get($selector);

    $user->SetClientCustomerId($CurClientID); 

    $GLOBALS["Root_Act_Num_"] = $page->customerId;    
  /*echo '<pre>';
      print_r($page);echo '</pre>';//exit;
*/    return array($page->descriptiveName, $page->customerId,  $page->canManageClients); 

}//function GetChildAccountsList_from_CustomerService(&$user)


function GetChildAccountsList(&$user)

{                                                                             

  $childAccounts = array();       

  $graph = GetAccountsGraph($user); 
	
  if (isset($graph->entries)) 
  {     

    $childLinks = array(); 

    $parentLinks = array(); 

    if (isset($graph->links)) 
    {

      foreach ($graph->links as $link) 
      {

        $childLinks[$link->managerCustomerId][] = $link; 

        $parentLinks[$link->clientCustomerId][] = $link; 

      }

    }//if (isset($graph->links)) 

    

    $accounts = array();

    $rootAccount = NULL; 

    foreach ($graph->entries as $account) 

    {

      $accounts[$account->customerId] = $account; 

      if (!array_key_exists($account->customerId, $parentLinks)) 

        $rootAccount = $account;      

    }//foreach ($graph->entries as $account) 

    

    if (!isset($rootAccount))

    {

      $rootAccount = new Account(); 

      $rootAccount->customerId = 0;
	 
      $rootAccount->name = $user->GetEmail(); 

      //$rootAccount->login = $user->GetEmail(); 

    }//if (!isset($rootAccount))    

    $GLOBALS["rootClientID"] = $rootAccount->customerId; 

    $childAccounts =  GetAccountTree($rootAccount, $accounts, $childLinks); 

  }//if (isset($graph->entries))       

  return $childAccounts;  //return array with child accounts list 

}//function GetChildAccountsList(&$user)


function GetAccountsGraph(&$user)

{                                                    

  $CurClientID = $user->GetClientCustomerId();

  $user->SetClientCustomerId(!empty($GLOBALS["Root_Act_Num_"]) ? $GLOBALS["Root_Act_Num_"] : null );   

  $managedCustomerService = $user->GetService('ManagedCustomerService', ADWORDS_VERSION);  

  $selector = new Selector();  

  $selector->fields = array( 'CustomerId',  'Name', 'DateTimeZone', 'CurrencyCode', 'CompanyName', 'CanManageClients');

  $selector->enablePaging = FALSE; 

  $user->SetClientCustomerId($CurClientID); 

  return $managedCustomerService->get($selector); 

}//function GetAccountsGraph(&$user)

function GetAccountTree($account, $accounts, $links) 

{ 

  $childAccounts = array(); 

  $childAccountsCounter = 0; 

  if (array_key_exists($account->customerId, $links)) 

  {

    foreach ($links[$account->customerId] as $childLink) 

    {

      $childAccount = $accounts[$childLink->clientCustomerId]; 

      $childAccounts[$childAccountsCounter++] = $childAccount; 

      $subchildAccounts = GetAccountTree($childAccount, $accounts, $links); 

      foreach ($subchildAccounts as $subchildAccount)  

       $childAccounts[$childAccountsCounter++] = $subchildAccount; 

    }//foreach ($links[$account->customerId] as $childLink) 

  }//if (array_key_exists($account->customerId, $links)) 

  return $childAccounts; //return $childAccounts array

}//function GetAccountTree($account, $accounts, $links) 


function GetTextAdsDataFromReportEx_bk(AdWordsUser &$user, $CurClientID,$StartDate, $EndDate)

{   
    
    $PredicatesArray = array();    

    /*if($AdGroupIDs != null && count($AdGroupIDs) > 0)

       array_push($PredicatesArray, new Predicate('AdGroupId', 'IN', $AdGroupIDs));     */

    $date = date('Y-m-d',strtotime('-1 day'));
  array_push($PredicatesArray, new Predicate('CampaignStatus', 'EQUALS', 'ENABLED')); 
  array_push($PredicatesArray, new Predicate('AdGroupStatus', 'EQUALS', 'ENABLED')); 
  array_push($PredicatesArray, new Predicate('Status', 'EQUALS', 'ENABLED')); 
    array_push($PredicatesArray, new Predicate('AdType', 'EQUALS', 'TEXT_AD'));
  //array_push($PredicatesArray, new Predicate('AdNetworkType1', 'EQUALS', 'SEARCH'));

    

    /*if($CampaignIDs != null && count($CampaignIDs) > 0)

       array_push($PredicatesArray, new Predicate('CampaignId', 'IN', $CampaignIDs));*/     

    

   /* if($ConvCount > 0)   

       array_push($PredicatesArray, new Predicate('ConvertedClicks', 'GREATER_THAN_EQUALS', (float)$ConvCount ));     
*/
    //else

      // array_push($PredicatesArray, new Predicate('Cost', 'GREATER_THAN_EQUALS', 1));     

       

          
  
    return GetCampaignsData_through_AdHoc_ReportsEx($user, $CurClientID,

                                                       'AD_PERFORMANCE_REPORT', 

                                                       'Ads Performance Report ',

                                                       array('Date','Description1','Description2','DisplayUrl','CreativeDestinationUrl','CreativeFinalUrls','CreativeFinalMobileUrls','CreativeTrackingUrlTemplate','CreativeUrlCustomParameters','Id','CampaignId','CampaignName','AdGroupName', 'Status','Ctr','AdType','AverageCpc', 'Cost', 'Clicks', 'AveragePosition','Impressions','Labels','Headline','KeywordId','Engagements','EngagementRate','AverageCpe','AdGroupId'),                                                       

                                                       $StartDate,

                                                       $EndDate,

                                                       $PredicatesArray);   

}//function GetTextAdsDataFromReportEx(AdWordsUser &$user, $CurClientID, $AdGroupIDs = null, $StartDate, $EndDate, $ConvCount=0, $CampaignIDs = null)
function GetTextAdsDataFromReportEx(AdWordsUser &$user, $CurClientID, $StartDate, $EndDate){   

    $PredicatesArray = array();    

   
  if($CurClientID!=9863942035){
    array_push($PredicatesArray, new Predicate('CampaignStatus', 'EQUALS', 'ENABLED')); 
  }
  //array_push($PredicatesArray, new Predicate('AdGroupStatus', 'EQUALS', 'ENABLED')); 
  array_push($PredicatesArray, new Predicate('Status', 'EQUALS', 'ENABLED')); 
    array_push($PredicatesArray, new Predicate('AdType', 'EQUALS', 'TEXT_AD')); 
  
  //array_push($PredicatesArray, new Predicate('AdNetworkType1', 'EQUALS', 'SEARCH'));          
          

    return GetCampaignsData_through_AdHoc_ReportsEx($user, $CurClientID,

                                                       'AD_PERFORMANCE_REPORT', 

                                                       'Ads Performance Report ',

                                                       array('Date','Description1','Description2','DisplayUrl','CreativeDestinationUrl','CreativeFinalUrls','CreativeFinalMobileUrls','CreativeTrackingUrlTemplate','CreativeUrlCustomParameters','Id','CampaignId','CampaignName','AdGroupName', 'Status','Ctr','AdType','AverageCpc', 'Cost', 'Clicks', 'AveragePosition','Impressions','Labels','Headline','KeywordId','Engagements','EngagementRate','AverageCpe','AdGroupId'
),                                                       

                                                        $StartDate,

                                                       $EndDate,

                                                       $PredicatesArray);   

}//function GetTextAdsDataFromReportEx(AdWordsUser &$user, $CurClientID, $AdGroupIDs = null, $StartDate, $EndDate, $ConvCount=0, $CampaignIDs = null)

//function GetCampaignsData_through_AdHoc_ReportsEx(AdWordsUser &$user, $CurClientID, $ReportType, $ReportName, $ReportFields, $StartDate, $EndDate, $Predicates = null, $filePath = null) 
function GetCampaignsData_through_AdHoc_ReportsEx(AdWordsUser &$user, $CurClientID, $ReportType, $ReportName, $ReportFields, $StartDate, $EndDate, $Predicates = null, $filePath = null){  

  $OldClientID = $user->GetClientCustomerId();

  if($CurClientID == -1)

    $CurClientID = "7650647282"; 

  $user->SetClientCustomerId($CurClientID); 

  $user->LoadService('ReportDefinitionService', ADWORDS_VERSION); 

  $selector = new Selector(); 

  $selector->fields = $ReportFields; 

  if($Predicates != null) 

   if(count($Predicates > 0)) 

    $selector->predicates = $Predicates; 

  $reportDefinition = new ReportDefinition();              

  $reportDefinition->selector = $selector;  
  $reportDefinition->reportName = $ReportName.'#' . uniqid();

  if($EndDate == null) {

    $reportDefinition->dateRangeType = $StartDate; 
   //$selector->dateRange = $StartDate;
  }
  else{                                                           

    $reportDefinition->dateRangeType = 'CUSTOM_DATE';  
   $selector->dateRange = new DateRange($StartDate, $EndDate); 
  }
    

  $reportDefinition->reportType = $ReportType; 

  $reportDefinition->downloadFormat = 'CSV'; 

    

  //$reportDefinition->includeZeroImpressions = true; 

    

  $options = array('version' => ADWORDS_VERSION/*, 'returnMoneyInMicros' => FALSE*/); 
 $options['skipReportHeader'] = true;
 
  $CSVDataFromReport = ReportUtils::DownloadReport($reportDefinition, $filePath, $user, $options); 

  //$ReportAsStr2DArray = ParseReportFromOneStrCSVToStr2DArrayEx($CSVDataFromReport);  

  

  $user->SetClientCustomerId($OldClientID);  

  return $CSVDataFromReport;

}//function GetCampaignsData_through_AdHoc_ReportsEx(AdWordsUser &$user, $CurClientID, $ReportType, $ReportName, $ReportFields, $StartDate, $EndDate, $Predicates = null, $filePath = null) 


function GetCampaignsData_through_AdHoc_ReportsEx_bk(AdWordsUser &$user, $CurClientID, $ReportType, $ReportName, $ReportFields, $StartDate, $EndDate, $Predicates = null, $filePath = null){  

  $OldClientID = $user->GetClientCustomerId();

  if($CurClientID == -1)

    $CurClientID = "7650647282"; 

  $user->SetClientCustomerId($CurClientID); 

  $user->LoadService('ReportDefinitionService', ADWORDS_VERSION); 

  $selector = new Selector(); 

  $selector->fields = $ReportFields; 

  if($Predicates != null) 

   if(count($Predicates > 0)) 

    $selector->predicates = $Predicates; 

  $reportDefinition = new ReportDefinition();              
 
  $reportDefinition->selector = $selector;  
  $reportDefinition->reportName = $ReportName.'#' . uniqid();

  if($EndDate == null) {

    $reportDefinition->dateRangeType = $StartDate; 
   //$selector->dateRange = $StartDate;
  }
  /*else{                                                           

    $reportDefinition->dateRangeType = 'CUSTOM_DATE';  
   $selector->dateRange = new DateRange($StartDate, $EndDate); 
  }*/
    

  $reportDefinition->reportType = $ReportType; 

  $reportDefinition->downloadFormat = 'CSV'; 

    

  //$reportDefinition->includeZeroImpressions = true; 

   
    ini_set('memory_limit','1024M');
    ini_set('max_execution_time', 1700);
    $options = array('version' => ADWORDS_VERSION/*, 'returnMoneyInMicros' => FALSE*/); 
    $options['skipReportHeader'] = true;
    
  $CSVDataFromReport = ReportUtils::DownloadReport($reportDefinition, $filePath, $user, $options); 
    /*$memory_in_use = memory_get_usage() / 1000000;
    echo round($memory_in_use, 1).' MB';
    exit;*/
  //$ReportAsStr2DArray = ParseReportFromOneStrCSVToStr2DArrayEx($CSVDataFromReport);  

  

  $user->SetClientCustomerId($OldClientID);  

  return $CSVDataFromReport;

}//function GetCampaignsData_through_AdHoc_ReportsEx(AdWordsUser &$user, $CurClientID, $ReportType, $ReportName, $ReportFields, $StartDate, $EndDate, $Predicates = null, $filePath = null) 
function csv_to_array($string='', $row_delimiter="\n", $delimiter = "," , $enclosure = '"' , $escape = "\\" )
{
    $rows = array_filter(explode($row_delimiter, $string));
    $header = NULL;
    $data = array();
  //echo 'AllKeywordsAsCSV';//break;  
    foreach($rows as $row)
    {
        try{
      //echo memory_get_usage() . "<br>";
    $row = str_getcsv ($row, $delimiter, $enclosure , $escape);
//echo '<pre>';print_r($row);echo '</pre>';
        if(!$header){ //echo '<pre>';print_r($row);echo '</pre>';
      //$row[0] = 'AdGroupID';
            $header = $row;
    }
        else{
      if(count($header) == count($row))
            $data[] = array_combine($header, $row);
    }
    }catch (Exception $e) {
        echo $e->getMessage();
      exit;//return;
    }
    }
//echo '<pre>';print_r($data);echo '</pre>';
    return $data;
}
function csv_to_array2($string='', $row_delimiter="\n", $delimiter = "," , $enclosure = '"' , $escape = "\\" )
{
  
    $rows = array_filter(explode($row_delimiter, $string));
  
    $header = NULL;
    $data = array();
  //echo 'AllKeywordsAsCSV';//break;
  ini_set('memory_limit','1024M');
  ini_set('max_execution_time', 7000);
  $id = time();
  $CI = &get_instance();
  $CI->db->query('TRUNCATE TABLE `data` '); 
    foreach($rows as $row)
    {
        try{
      //echo memory_get_usage() . "<br>";
    $row = str_getcsv ($row, $delimiter, $enclosure , $escape);
    
//echo '<pre>';print_r($row);echo '</pre>';
        if(!$header){ //echo '<pre>';print_r($row);echo '</pre>';
      //$row[0] = 'AdGroupID';
            $header = $row;
      //echo '<pre>';print_r($header);echo '</pre>';exit;
    }
        else{
      if(count($header) == count($row)){
            //$data[] = array_combine($header, $row);
        
        $dbData['day'] = $row[0];
        $dbData['descriptionLine1'] = $row[1];
        $dbData['descriptionLine2'] = $row[2];
        $dbData['displayURL'] = $row[3];
        $dbData['destinationURL'] = $row[4];
        $dbData['finalURL'] = $row[5];
        $dbData['mobileFinalURL'] = $row[6];
        $dbData['trackingTemplate'] = $row[7];
        $dbData['customParameter'] = $row[8];
        $dbData['adID'] = $row[9];
        $dbData['campaign'] = $row[10];
        $dbData['adGroup'] = $row[11];
        $dbData['status'] = $row[12];
        $dbData['ctr'] = $row[13];
        $dbData['adType'] = $row[14];
        $dbData['avgCPC'] = $row[15];
        $dbData['cost'] = $row[16];
        $dbData['clicks'] = $row[17];
        $dbData['avgPosition'] = $row[18];
        $dbData['impressions'] = $row[19];
        $dbData['labels'] = $row[20];
        $dbData['ad'] = $row[21];
        $dbData['kid'] = $row[22];
        $dbData['engagements'] = $row[23];
        $dbData['engagementRate'] = $row[24];
        $dbData['avgcpe'] = $row[25];
        $dbData['uid'] = $id;
        $CI->db->insert('data',$dbData);
      }
    }
    }catch (Exception $e) {
        echo $e->getMessage();
      exit;//return;
    }
    }
//echo '<pre>';print_r($data);echo '</pre>';
  $data = $CI->db->query('SELECT * FROM data WHERE uid = '.$id.'');
    return $data;
}

function GetTextAdsDataFromReportExKeywords(AdWordsUser &$user, $CurClientID,$StartDate, $EndDate)

{   
    
    $PredicatesArray = array();    

    $date = date('Y-m-d',strtotime('-1 day'));
	if($CurClientID!=9863942035){
    array_push($PredicatesArray, new Predicate('CampaignStatus', 'EQUALS', 'ENABLED')); 
  }
  //array_push($PredicatesArray, new Predicate('CampaignStatus', 'EQUALS', 'ENABLED')); 
  array_push($PredicatesArray, new Predicate('AdGroupStatus', 'EQUALS', 'ENABLED')); 
  array_push($PredicatesArray, new Predicate('Status', 'EQUALS', 'ENABLED')); 
          
  return GetCampaignsData_through_AdHoc_ReportsEx($user, $CurClientID,

                                                       'KEYWORDS_PERFORMANCE_REPORT', 

                                                       'KEYWORDS Performance Report ',

                                                       array('Id','Criteria','KeywordMatchType'),                                                       

                                                       $StartDate,

                                                       $EndDate,

                                                       $PredicatesArray);
    

}//function GetTextAdsDataFromReportEx(AdWordsUser &$user, $CurClientID, $AdGroupIDs = null, $StartDate, $EndDate, $ConvCount=0, $CampaignIDs = null)

function csv_to_array1($string='', $row_delimiter="\n", $delimiter = "," , $enclosure = '"' , $escape = "\\" )
{
  
    $rows = array_filter(explode($row_delimiter, $string));
  $id = time();
    $header = NULL;
    $data = '';
  //echo 'AllKeywordsAsCSV';//break;
  ini_set('memory_limit','1024M');
  ini_set('max_execution_time', 7000);
  $CI = &get_instance();  
  $CI->db->query('TRUNCATE TABLE `report_keywords` ');
    foreach($rows as $row)
    {
        try{
      //echo memory_get_usage() . "<br>";
    $row = str_getcsv ($row, $delimiter, $enclosure , $escape);
    
//echo '<pre>';print_r($row);echo '</pre>';
        if(!$header){ //echo '<pre>';print_r($row);echo '</pre>';
      //$row[0] = 'AdGroupID';
            $header = $row;
    }
        else{
      if(count($header) == count($row)){
            //$data[] = array_combine($header, $row);
        $CI = &get_instance();
        $dbData['idk'] = $row[0];
        $dbData['keyword'] = $row[1];
        $dbData['MatchType'] = $row[2];
        $dbData['uid'] = $id;
        $CI->db->insert('report_keywords',$dbData);
      }
    }
    }catch (Exception $e) {
        echo $e->getMessage();
      exit;//return;
    }
    }
  $data = $CI->db->query('SELECT * FROM report_keywords WHERE uid = '.$id.'');
    return $data;
}

function GetTextAdsDataFromReportExCriteria(AdWordsUser &$user, $CurClientID,$StartDate, $EndDate)

{   
    
    $PredicatesArray = array();    

    /*if($AdGroupIDs != null && count($AdGroupIDs) > 0)

       array_push($PredicatesArray, new Predicate('AdGroupId', 'IN', $AdGroupIDs));     */

    $date = date('Y-m-d',strtotime('-1 day'));
  array_push($PredicatesArray, new Predicate('CampaignStatus', 'EQUALS', 'ENABLED')); 
  array_push($PredicatesArray, new Predicate('AdGroupStatus', 'EQUALS', 'ENABLED')); 
  array_push($PredicatesArray, new Predicate('Status', 'EQUALS', 'ENABLED')); 
    array_push($PredicatesArray, new Predicate('AdType', 'EQUALS', 'TEXT_AD'));
    array_push($PredicatesArray, new Predicate('AdNetworkType1', 'EQUALS', 'CONTENT'));

    

    /*if($CampaignIDs != null && count($CampaignIDs) > 0)

       array_push($PredicatesArray, new Predicate('CampaignId', 'IN', $CampaignIDs));*/     

    

   /* if($ConvCount > 0)   

       array_push($PredicatesArray, new Predicate('ConvertedClicks', 'GREATER_THAN_EQUALS', (float)$ConvCount ));     
*/
    //else

      // array_push($PredicatesArray, new Predicate('Cost', 'GREATER_THAN_EQUALS', 1));     

       

          
  
    return GetCampaignsData_through_AdHoc_ReportsEx($user, $CurClientID,

                                                       'AD_PERFORMANCE_REPORT', 

                                                       'Ads Performance Report ',

                                                       array('Date','Description1','Description2','DisplayUrl','CreativeDestinationUrl','CreativeFinalUrls','CreativeFinalMobileUrls','CreativeTrackingUrlTemplate','CreativeUrlCustomParameters','Id','CampaignId','CampaignName','AdGroupName', 'Status','Ctr','AdType','AverageCpc', 'Cost', 'Clicks', 'AveragePosition','Impressions','Labels','Headline','KeywordId','Engagements','EngagementRate','AverageCpe'),                                                       

                                                       $StartDate,

                                                       $EndDate,

                                                       $PredicatesArray);    

}//function GetTextAdsDataFromReportEx(AdWordsUser &$user, $CurClientID, $AdGroupIDs = null, $StartDate, $EndDate, $ConvCount=0, $CampaignIDs = null)

function GetTextAdsDataFromReportExVedio(AdWordsUser &$user, $CurClientID,$StartDate, $EndDate)

{   
    
    $PredicatesArray = array();    

    /*if($AdGroupIDs != null && count($AdGroupIDs) > 0)

       array_push($PredicatesArray, new Predicate('AdGroupId', 'IN', $AdGroupIDs));     */

    $date = date('Y-m-d',strtotime('-1 day'));
  array_push($PredicatesArray, new Predicate('CampaignStatus', 'EQUALS', 'ENABLED')); 
  array_push($PredicatesArray, new Predicate('AdGroupStatus', 'EQUALS', 'ENABLED')); 
  array_push($PredicatesArray, new Predicate('CreativeStatus', 'EQUALS', 'ENABLED')); 

    

    /*if($CampaignIDs != null && count($CampaignIDs) > 0)

       array_push($PredicatesArray, new Predicate('CampaignId', 'IN', $CampaignIDs));*/     

    

   /* if($ConvCount > 0)   

       array_push($PredicatesArray, new Predicate('ConvertedClicks', 'GREATER_THAN_EQUALS', (float)$ConvCount ));     
*/
    //else

      // array_push($PredicatesArray, new Predicate('Cost', 'GREATER_THAN_EQUALS', 1));     

       

          
  
    return GetCampaignsData_through_AdHoc_ReportsEx($user, $CurClientID,

                                                       'VIDEO_PERFORMANCE_REPORT', 

                                                       'Video Performance Report ',

                                                       array('Date','CreativeStatus','CreativeId','CampaignId','CampaignName','AdGroupName','Ctr', 'Cost', 'Clicks','Impressions','Engagements','EngagementRate'),                                                       

                                                       $StartDate,

                                                       $EndDate,

                                                       $PredicatesArray);   

}

function GetTextAdsDataFromReportExTest(AdWordsUser &$user, $CurClientID,$StartDate, $EndDate)

{   
    
    $PredicatesArray = array();    

    /*if($AdGroupIDs != null && count($AdGroupIDs) > 0)

       array_push($PredicatesArray, new Predicate('AdGroupId', 'IN', $AdGroupIDs));     */

    $date = date('Y-m-d',strtotime('-1 day'));
  array_push($PredicatesArray, new Predicate('CampaignStatus', 'EQUALS', 'ENABLED')); 
  array_push($PredicatesArray, new Predicate('AdGroupStatus', 'EQUALS', 'ENABLED')); 
  array_push($PredicatesArray, new Predicate('Status', 'EQUALS', 'ENABLED')); 
    array_push($PredicatesArray, new Predicate('AdType', 'EQUALS', 'TEXT_AD'));
  //array_push($PredicatesArray, new Predicate('AdNetworkType1', 'EQUALS', 'SEARCH'));
  array_push($PredicatesArray, new Predicate('AdNetworkType2', 'EQUALS', 'CONTENT'));

    

    /*if($CampaignIDs != null && count($CampaignIDs) > 0)

       array_push($PredicatesArray, new Predicate('CampaignId', 'IN', $CampaignIDs));*/     

    

   /* if($ConvCount > 0)   

       array_push($PredicatesArray, new Predicate('ConvertedClicks', 'GREATER_THAN_EQUALS', (float)$ConvCount ));     
*/
    //else

      // array_push($PredicatesArray, new Predicate('Cost', 'GREATER_THAN_EQUALS', 1));     

       

          
  
    return GetCampaignsData_through_AdHoc_ReportsEx($user, $CurClientID,

                                                       'AD_PERFORMANCE_REPORT', 

                                                       'Ads Performance Report ',

                                                       array('Date','Description1','Description2','DisplayUrl','CreativeDestinationUrl','CreativeFinalUrls','CreativeFinalMobileUrls','CreativeTrackingUrlTemplate','CreativeUrlCustomParameters','Id','CampaignId','CampaignName','AdGroupName', 'Status','Ctr','AdType','AverageCpc', 'Cost', 'Clicks', 'AveragePosition','Impressions','Labels','Headline','KeywordId','Engagements','EngagementRate','AverageCpe','AdNetworkType1'),                                                       

                                                       $StartDate,

                                                       $EndDate,

                                                       $PredicatesArray);   

}

function getAdgroupSettings(AdWordsUser $user,$campaignId,$CurClientID){
   if($CurClientID == -1){
    $CurClientID = "7650647282";
    }
  
    $user->SetClientCustomerId($CurClientID);
   $adGroupService = $user->GetService('AdGroupService', ADWORDS_VERSION);
    
    // Create selector.
    $selector = new Selector();
    $selector->fields = array('Id', 'Name','Settings');
    $selector->ordering[] = new OrderBy('Name', 'ASCENDING');
  
    // Create predicates.
    $selector->predicates[] =
      new Predicate('CampaignId', 'IN', array($campaignId));
  
    // Create paging controls.
    $selector->paging = new Paging(0, AdWordsConstants::RECOMMENDED_PAGE_SIZE);
  
    do {
     
    // Make the get request.
    $page = $adGroupService->get($selector);
  
    // Display results.
    if (isset($page->entries)) {
      foreach ($page->entries as $adGroup) {
        echo '<pre>';
        print_r($adGroup);
      /*printf("Ad group with name '%s' and ID '%s' was found.\n",
        $adGroup->name, $adGroup->id);*/
      }
    } else {
      print "No ad groups were found.\n";
    }
  
    // Advance the paging index.
    $selector->paging->startIndex += AdWordsConstants::RECOMMENDED_PAGE_SIZE;
    } while ($page->totalNumEntries > $selector->paging->startIndex);
  }
  
  
  function getCampaignsSettings(AdWordsUser $user,$campaignId,$CurClientID){
   if($CurClientID == -1){
    $CurClientID = "7650647282";
    }
  
    $user->SetClientCustomerId($CurClientID);
    $campaignService = $user->GetService('CampaignService', ADWORDS_VERSION);
  
    // Create selector.
    $selector = new Selector();
    $selector->fields = array('Id', 'Name','Settings');
    $selector->ordering[] = new OrderBy('Name', 'ASCENDING');
  
    // Create paging controls.
    $selector->paging = new Paging(0, AdWordsConstants::RECOMMENDED_PAGE_SIZE);
  
    do {
    // Make the get request.
    $page = $campaignService->get($selector);
  
    // Display results.
    if (isset($page->entries)) {
      foreach ($page->entries as $campaign) {
        echo '<pre>';
        print_r($campaign);
      /*printf("Campaign with name '%s' and ID '%s' was found.\n",
        $campaign->name, $campaign->id);*/
      }
    } else {
      print "No campaigns were found.\n";
    }
  
    // Advance the paging index.
    $selector->paging->startIndex += AdWordsConstants::RECOMMENDED_PAGE_SIZE;
    } while ($page->totalNumEntries > $selector->paging->startIndex);
  }
  
function GetCampaignDataFromReportEx(AdWordsUser &$user, $CurClientID, $CampaignIDs = null, $date_range, $WithAtLeastOneConv=false)
{       

   // GetDatesFromPeriodForBidding($StartDate, $EndDate, $PeriodCode);    

    $PredicatesArray = array();    
   /* if($CampaignIDs != null && count($CampaignIDs) > 0)

       array_push($PredicatesArray, new Predicate('Id', 'IN', $CampaignIDs));     
*/
    

    //if($WithAtLeastOneConv)   

       //array_push($PredicatesArray, new Predicate('ConvertedClicks', 'GREATER_THAN_EQUALS', 1.0));     

    //else

      // array_push($PredicatesArray, new Predicate('Cost', 'GREATER_THAN_EQUALS', 0.01));     
//echo '<pre>';
  //  print_r($PredicatesArray);   

    //exit();

   /* array_push($PredicatesArray, new Predicate('CampaignStatus', 'EQUALS', 'ENABLED'));     
  array_push($PredicatesArray, new Predicate('AdvertisingChannelType', 'EQUALS','SEARCH'));     
  array_push($PredicatesArray, new Predicate('AdNetworkType1', 'EQUALS', 'SEARCH')); 
*/    return GetCampaignsData_through_AdHoc_ReportsEx($user, $CurClientID,

                                                       'CAMPAIGN_PERFORMANCE_REPORT', 

                                                       'Campaign Performance Report ',

                                                       array('CampaignId', 'CampaignName', 'CampaignStatus'),                      
                               //array('AdGroupId','AdGroupName'),                                                       

                                                       $date_range[0],

                                                       $date_range[1],

                                                       $PredicatesArray);   

}//function GetAdGroupsDataFromReportEx(AdWordsUser &$user, $CurClientID, $AdGroupIDs = null, $PeriodCode)

function GetKeywordsDataFromReportEx(AdWordsUser &$user, $CurClientID, $AdGroupIDs = null, $date_range, $WithAtLeastOneConv=true, $CampaignIDs = null)
{       
  $StartDate = $date_range[0];
  $EndDate = $date_range[1];
   // GetDatesFromPeriodForBidding($StartDate, $EndDate, $PeriodCode);    

    $PredicatesArray = array();    

   /* if($AdGroupIDs != null && count($AdGroupIDs) > 0)  

       array_push($PredicatesArray, new Predicate('AdGroupId', 'IN', $AdGroupIDs));     

  */  

    if($CampaignIDs != null && count($CampaignIDs) > 0)

       array_push($PredicatesArray, new Predicate('CampaignId', 'IN', $CampaignIDs));     
   /* if($WithAtLeastOneConv)   

       array_push($PredicatesArray, new Predicate('ConvertedClicks', 'GREATER_THAN_EQUALS', 1.0));     

    else*/ 

      // array_push($PredicatesArray, new Predicate('Impressions', 'GREATER_THAN_EQUALS',0.0));
    // array_push($PredicatesArray, new Predicate('ConvertedClicks', 'EQUALS', 0)); 
    
    //commenting now
    /* array_push($PredicatesArray, new Predicate('IsNegative','IN', array(true,false)));   
     array_push($PredicatesArray, new Predicate('CriteriaType', 'EQUALS', 'KEYWORD'));
     array_push($PredicatesArray, new Predicate('AdNetworkType1', 'EQUALS', 'SEARCH'));
     array_push($PredicatesArray, new Predicate('CampaignStatus', 'EQUALS', 'ENABLED')); 
     array_push($PredicatesArray, new Predicate('Status', 'EQUALS', 'ENABLED')); 
     array_push($PredicatesArray, new Predicate('ApprovalStatus', 'EQUALS', 'APPROVED')); 
      array_push($PredicatesArray, new Predicate('AdGroupStatus', 'EQUALS', 'ENABLED')); 
*/     
  
  
  
  // array_push($PredicatesArray, new Predicate('IsNegative', 'true'));     
  //echo '<pre>';print_r($PredicatesArray);echo '</pre>';
    return GetCampaignsData_through_AdHoc_ReportsEx($user, $CurClientID,  

                                                       'CRITERIA_PERFORMANCE_REPORT', 
                                                       'Keywords Performance Report ',
                                                     // array('IsNegative','CriteriaType', 'BidType', 'ApprovalStatus', 'AdNetworkType2')
                            array('Id','CampaignId','CampaignName', 'AdGroupId','AdGroupName','Status', 'Criteria')              /* array('Clicks') */               
,                                                       

                                                       $StartDate,
 
                                                       $EndDate,

                                                       $PredicatesArray);   

}


function getChildAccount($token,$ClientID){
	
	$user = new AdWordsUser();
	PrevInitAdwordsUserSettings($user);
	if(LoadAdWordsAccessTokenForClient($user, $token) ){ 
		//echo $MCCAccountName;
			$child_accounts = array();
			$ChildAccountsList = GetChildAccountsListAsAr($user, $MCCAccountName);
			foreach($ChildAccountsList as $CurChildAccount){
						$child_accounts[] = array('client_id'=>$ClientID,
										  'name'=>$CurChildAccount[1],
										  'account_id'=>$CurChildAccount[0]);
					
			}
			if(count($child_accounts)>0){
				return $child_accounts;
			}else{
				return false;
			}
		}
	return $user;
}

function getAccountCpc($token,$ClientID,$f){
	if($f == 'Cpa'){
		$f = 'CostPerAllConversion';
	}
	$user = new AdWordsUser();
	PrevInitAdwordsUserSettings($user);
	$cpc = 0;
	if(LoadAdWordsAccessTokenForClient($user, $token) ){ 
		$user->SetClientCustomerId($ClientID); 
		$reportQuery = 'SELECT '.$f.' FROM ACCOUNT_PERFORMANCE_REPORT '
      . 'WHERE ExternalCustomerId = '.$ClientID.'';

		$options = array('version' => ADWORDS_VERSION);
		$name = time().'.csv';
		$filePath = $_SERVER['DOCUMENT_ROOT'].'/admetric/csv/'.$name;
		$reportFormat = 'CSV';
		ReportUtils::DownloadReportWithAwql($reportQuery, $filePath, $user,
		$reportFormat, $options);
		$file = fopen($filePath,"r");
		$i=1;
		
		while(!feof($file))
		  {
			 $cpcArray = fgetcsv($file);
		  	if($i==3){
				$cpc = $cpcArray[0];
			}
			if($i>3){
				break;
			}
			$i++;
		  }

		fclose($file);
		unlink($filePath);
		
		}
	return $cpc;
}

function getAccountCampaigns($token,$ClientID){
	//$current = round(microtime(true) * 1000);
	$user = new AdWordsUser();
	PrevInitAdwordsUserSettings($user);
	$campains = array();
	if(LoadAdWordsAccessTokenForClient($user, $token) ){ 
		$user->SetClientCustomerId($ClientID); 
		
		$reportQuery = 'SELECT CampaignId,CampaignName,AverageCpe FROM CAMPAIGN_PERFORMANCE_REPORT '
      . 'WHERE ExternalCustomerId = '.$ClientID;

		$options = array('version' => ADWORDS_VERSION);
		$name = 'c'.time().'.csv';
		$filePath = $_SERVER['DOCUMENT_ROOT'].'/admetric/csv/'.$name;
		$reportFormat = 'CSV';
		//$filePath = NULL;
		ReportUtils::DownloadReportWithAwql($reportQuery, $filePath, $user,
		$reportFormat, $options);
		//echo ((round(microtime(true) * 1000))-$current).'<br>';
		//echo '<pre>';
		//print_r($reportData);
		//exit;
		$file = fopen($filePath,"r");
		$i=1;
		
		while(!feof($file))
		  {
			 $cpcArray = fgetcsv($file);
		  	if($i>2){
				$newArr = array();
				$newArr['id'] = $cpcArray[0];
				$newArr['name'] = $cpcArray[1];
				$newArr['cpc'] = $cpcArray[2];
				$campains[] = $newArr;
			}
			/*if($i>3){
				break;
			}*/
			$i++;
		  }

		fclose($file);
		unlink($filePath);
		
		}
	$in = (count($campains)-1);
	unset($campains[$in]);
	unset($campains[($in-1)]);
	return $campains;
}

function getAccountCampaignsAsString($token,$ClientID,$f){
	if($f == 'Cpa'){
		$f = 'CostPerAllConversion';
	}
	//$current = round(microtime(true) * 1000);
	$user = new AdWordsUser();
	PrevInitAdwordsUserSettings($user);
	$campains = array();
	if(LoadAdWordsAccessTokenForClient($user, $token) ){ 
		$user->SetClientCustomerId($ClientID); 
		
		$reportQuery = 'SELECT CampaignId,CampaignName,'.$f.' FROM CAMPAIGN_PERFORMANCE_REPORT '
      . 'WHERE ExternalCustomerId = '.$ClientID.' AND CampaignStatus = ENABLED';

		$options = array('version' => ADWORDS_VERSION);
		$name = 'c'.time().'.csv';
		$filePath = $_SERVER['DOCUMENT_ROOT'].'/admetric/csv/'.$name;
		$reportFormat = 'CSV';
		$filePath = NULL;
		$reportData = ReportUtils::DownloadReportWithAwql($reportQuery, $filePath, $user,
		$reportFormat, $options);
		$i=1;
		$Data = str_getcsv($reportData, "\n");
		foreach($Data as &$Row){
			$Row = str_getcsv($Row, ",");
		  	if($i>2){
				$newArr = array();
				$newArr['id'] = $Row[0];
				$newArr['name'] = $Row[1];
				$newArr['cpc'] = $Row[2];
				$newArr['a'] = $ClientID;
				$campains[] = $newArr;
			}
			$i++;
		  }
		}
	$in = (count($campains)-1);
	unset($campains[$in]);
	/*echo ((round(microtime(true) * 1000))-$current).'<br>';
	echo '<pre>';
	print_r($campains);exit;*/ 
	//unset($campains[($in-1)]);
	return $campains;
}

function getRootAccount($token){
	
	$user = new AdWordsUser();
	PrevInitAdwordsUserSettings($user);
	if(LoadAdWordsAccessTokenForClient($user, $token) ){ 
		//echo $MCCAccountName;
			$Account = GetChildAccountsListAsArRoot($user, $MCCAccountName);
			return $Account;
		}
	return $user;
}

function GetChildAccountsListAsArRoot(&$user, &$MCCAccountName)

{ 

    $ChildAccountsListA = array(); //define a string variable to store child accounts list in CSV format    
 
    $ChildAccountsList_from_CustomerService = GetChildAccountsList_from_CustomerService($user); //download child accounts list from 'CustomerService' of AdWords API 

    $IsMCCAccount = $ChildAccountsList_from_CustomerService[2];  //check if account is MCC

    $MCCAccountName = $ChildAccountsList_from_CustomerService[0];

    $returnArray = array();

    if($IsMCCAccount) //if account is MCC

    {
	   $returnArray['accountName']=  $MCCAccountName;
	   $returnArray['customerId']=  $ChildAccountsList_from_CustomerService[1];
	   $returnArray['isMCCAccount']=  $ChildAccountsList_from_CustomerService[2];
    }
     
	 return $returnArray;                       

}


function getCampaignsAdgroups($token,$ClientID,$cid){
	//$current = round(microtime(true) * 1000);
	$user = new AdWordsUser();
	PrevInitAdwordsUserSettings($user);
	$campains = array();
	if(LoadAdWordsAccessTokenForClient($user, $token) ){ 
		$user->SetClientCustomerId($ClientID); 
		
		$reportQuery = 'SELECT AdGroupId,AdGroupName FROM ADGROUP_PERFORMANCE_REPORT '
      . 'WHERE CampaignId = '.$cid.' AND AdGroupStatus = ENABLED';

		$options = array('version' => ADWORDS_VERSION);
		$name = 'c'.time().'.csv';
		$filePath = $_SERVER['DOCUMENT_ROOT'].'/admetric/csv/'.$name;
		$reportFormat = 'CSV';
		$filePath = NULL;
		$reportData = ReportUtils::DownloadReportWithAwql($reportQuery, $filePath, $user,
		$reportFormat, $options);
		$i=1;
		$Data = str_getcsv($reportData, "\n");
		
		foreach($Data as &$Row){
			$Row = str_getcsv($Row, ",");
		  	if($i>2){
				$newArr = array();
				$newArr['id'] = $Row[0];
				$newArr['name'] = $Row[1];
				$newArr['a'] = $ClientID;
				$newArr['c'] = $cid;
				$campains[] = $newArr;
				
			}
			$i++;
		  }
		}
	$in = (count($campains)-1);
	unset($campains[$in]);
	/*echo ((round(microtime(true) * 1000))-$current).'<br>';
	echo '<pre>';
	print_r($campains);exit;*/ 
	//unset($campains[($in-1)]);
	return $campains;
}

function getAdgroupAds($token,$ClientID,$aid){
	//$current = round(microtime(true) * 1000);
	$user = new AdWordsUser();
	PrevInitAdwordsUserSettings($user);
	$campains = array();
	if(LoadAdWordsAccessTokenForClient($user, $token) ){ 
		$user->SetClientCustomerId($ClientID); 
		
		$reportQuery = 'SELECT Id,Headline FROM AD_PERFORMANCE_REPORT '
      . 'WHERE AdGroupId = '.$aid.' AND Status = ENABLED';

		$options = array('version' => ADWORDS_VERSION);
		$name = 'c'.time().'.csv';
		$filePath = $_SERVER['DOCUMENT_ROOT'].'/admetric/csv/'.$name;
		$reportFormat = 'CSV';
		$filePath = NULL;
		$reportData = ReportUtils::DownloadReportWithAwql($reportQuery, $filePath, $user,
		$reportFormat, $options);
		$i=1;
		$Data = str_getcsv($reportData, "\n");
		
		foreach($Data as &$Row){
			$Row = str_getcsv($Row, ",");
		  	if($i>2){
				$newArr = array();
				$newArr['id'] = $Row[0];
				$newArr['name'] = $Row[1];
				$newArr['a'] = $ClientID;
				$newArr['ai'] = $aid;
				$campains[] = $newArr;
				
			}
			$i++;
		  }
		}
	$in = (count($campains)-1);
	unset($campains[$in]);
	/*echo ((round(microtime(true) * 1000))-$current).'<br>';
	echo '<pre>';
	print_r($campains);exit;*/ 
	//unset($campains[($in-1)]);
	return $campains;
}

function getAdgroupKeywords($token,$ClientID,$aid){
	//$current = round(microtime(true) * 1000);
	$user = new AdWordsUser();
	PrevInitAdwordsUserSettings($user);
	$campains = array();
	if(LoadAdWordsAccessTokenForClient($user, $token) ){ 
		$user->SetClientCustomerId($ClientID); 
		
		$reportQuery = 'SELECT Id,Criteria FROM KEYWORDS_PERFORMANCE_REPORT '
      . 'WHERE AdGroupId = '.$aid.' AND Status = ENABLED';

		$options = array('version' => ADWORDS_VERSION);
		$name = 'c'.time().'.csv';
		$filePath = $_SERVER['DOCUMENT_ROOT'].'/admetric/csv/'.$name;
		$reportFormat = 'CSV';
		$filePath = NULL;
		$reportData = ReportUtils::DownloadReportWithAwql($reportQuery, $filePath, $user,
		$reportFormat, $options);
		$i=1;
		$Data = str_getcsv($reportData, "\n");
		
		foreach($Data as &$Row){
			$Row = str_getcsv($Row, ",");
		  	if($i>2){
				$newArr = array();
				$newArr['id'] = $Row[0];
				$newArr['name'] = $Row[1];
				$newArr['a'] = $ClientID;
				$newArr['ai'] = $aid;
				$campains[] = $newArr;
				
			}
			$i++;
		  }
		}
	$in = (count($campains)-1);
	unset($campains[$in]);
	/*echo ((round(microtime(true) * 1000))-$current).'<br>';
	echo '<pre>';
	print_r($campains);exit;*/ 
	//unset($campains[($in-1)]);
	return $campains;
}

/*function getSettingCpc($token,$ClientID,$id = 0,$where,$report,$dateRange,$f){
	
	if($f == 'Cpa'){
		$f = 'CostPerAllConversion';
	}
	$user = new AdWordsUser();
	PrevInitAdwordsUserSettings($user);
	$cpc = 0;
	if(LoadAdWordsAccessTokenForClient($user, $token) ){ 
		$user->SetClientCustomerId($ClientID); 
		$reportQuery = 'SELECT '.$f.' FROM '.$report.' WHERE ';
		
		if($id == 0){
			$reportQuery .= ''.$where.' = '.$ClientID.'';
		}else{
			$reportQuery .= ''.$where.' = '.$id.'';
		}
		
		if($dateRange != ''){
			$reportQuery .= $dateRange;
		}
		//echo $reportQuery;exit;
		$options = array('version' => ADWORDS_VERSION);
		$name = time().'.csv';
		$filePath = $_SERVER['DOCUMENT_ROOT'].'/admetric/csv/'.$name;
		$reportFormat = 'CSV';
		$filePath = NULL;
		$reportData = ReportUtils::DownloadReportWithAwql($reportQuery, $filePath, $user,
		$reportFormat, $options);
		$Data = str_getcsv($reportData, "\n");
		
		if(count($Data) == 4){
			if($f == 'Ctr' || $f == 'ConversionRate'){
				$cpc = $Data[2];
			}else{
				$cpc = '$'.$Data[2];
				
			}
		}else{
			if($f == 'Ctr' || $f == 'ConversionRate'){
				$cpc = '0.00%';
			}else{
				$cpc = '$0';
				
			}
		}
		
		
	}
	return $cpc;
}
*/

function getSettingCpc($token,$ClientID,$id = 0,$where,$report,$dateRange,$f){
	
	if($f == 'Cpa'){
		$f = 'CostPerAllConversion';
	}
	$user = new AdWordsUser();
	PrevInitAdwordsUserSettings($user);
	$cpc = 0;
	if(LoadAdWordsAccessTokenForClient($user, $token) ){ 
		$user->SetClientCustomerId($ClientID); 
		$reportQuery = 'SELECT '.$f.' FROM '.$report.' WHERE ';
		
		if($id == 0){
			$reportQuery .= ''.$where.' = '.$ClientID.'';
		}else{
			$reportQuery .= ''.$where.' = '.$id.'';
		}
		
		if($dateRange != ''){
			$reportQuery .= $dateRange;
		}
		//echo $reportQuery;exit;
		$options = array('version' => ADWORDS_VERSION);
		$name = time().'.csv';
		$filePath = $_SERVER['DOCUMENT_ROOT'].'/admetric/csv/'.$name;
		$reportFormat = 'CSV';
		$filePath = NULL;
		$reportData = ReportUtils::DownloadReportWithAwql($reportQuery, $filePath, $user,
		$reportFormat, $options);
		$Data = str_getcsv($reportData, "\n");
		/*echo '<pre>';
		print_r($reportData);exit;*/
		$cpc = $Data[2];
		
		
		
	}
	return $cpc;
}
function getSettingAccounts($id){
	$affected = DB::select('
		SELECT
		*
		FROM
		chlidaccount
		WHERE
		caccount_id = '.$id.'
	');
	/*echo '<pre>';
	print_r($affected);exit;*/
	return $affected[0]->account_name;
}

function getSettingA($id){
	$account = DB::select('
		SELECT
		*
		FROM
		accounts
		WHERE
		id = '.$id.'
	');
	$affected = DB::select('
		SELECT
		*
		FROM
		chlidaccount
		WHERE
		account_id = '.$account[0]->mccaccount_id.'
	');
	$html = '';
	if(count($affected)>0){
		$html .= '<li style="height:25px !important; min-height:25px;" ><a class="childs" href="javascript:void(0);" data-id="0" data-val="0">Select Account</a></li>';
		foreach($affected as $row){
			$html .= '<li style="height:25px !important; min-height:25px;" ><a href="javascript:void(0);" data-id="'.$id .'"  data-val="'.$row->caccount_id.'" class="childs">'.$row->account_name.'</a></li>';
		}
	}
	return $html;
}

function getSettingAa($id){
	$affected = DB::select('
		SELECT
		*
		FROM
		chlidaccount
		WHERE
		account_id = '.$id.'
	');
	$a = 0;
	if(count($affected)>0){
		$a = $affected[0]->caccount_id;
		
	}
	return $a;
}

function getSettingCampaigns($id){
	$affected = DB::select('
		SELECT
		*
		FROM
		campaign
		WHERE
		caccount_id = '.$id.'
	');
	$html = '';
	if(count($affected)>0){
		$html .= '<li style="height:25px !important; min-height:25px;" ><a class="childsCampaigns" href="javascript:void(0);" data-id="0" data-val="0">Select Campaign</a></li>';
		foreach($affected as $row){
			$html .= '<li style="height:25px !important; min-height:25px;" ><a href="javascript:void(0);" data-id="'.$row->campaign_id .'"  data-val="'.$row->cpc.'"  data-account="'.$id.'" class="childsCampaigns">'.$row->campaign_name.'</a></li>';
		}
	}
	return $html;
}

function getSettingCampaignName($id){
	$affected = DB::select('
		SELECT
		*
		FROM
		campaign
		WHERE
		campaign_id = '.$id.'
	');
	
	/*echo '<pre>';
	print_r($affected);exit;*/
	return $affected[0]->campaign_name;
}

function getSettingAdgroupName($id){
	$affected = DB::select('
		SELECT
		*
		FROM
		adgroups
		WHERE
		adgroup_id = '.$id.'
	');
	
	/*echo '<pre>';
	print_r($affected);exit;*/
	return $affected[0]->adgroup_name;
}

function getSettingAdgroups($id){
	$affected = DB::select('
		SELECT
		*
		FROM
		adgroups
		WHERE
		campaign_id = '.$id.'
	');
	$html = '';
	if(count($affected)>0){
		$html .= '<li style="height:25px !important; min-height:25px;" ><a class="childsAdgroups" href="javascript:void(0);" data-id="0" data-val="0">Select Adgroup</a></li>';
		foreach($affected as $row){
			$html .= '<li style="height:25px !important; min-height:25px;" ><a href="javascript:void(0);" data-id="'.$row->adgroup_id .'"  data-aid="'.$row->account_id.'"  data-c="'.$row->campaign_id.'"  data-account="'.$row->caccount_id.'" class="childsAdgroups">'.$row->adgroup_name.'</a></li>';
			
		}
	}
	return $html;
}

function getSettingKeywordName($id){
	$affected = DB::select('
		SELECT
		*
		FROM
		adkeyword
		WHERE
		keyword_id = '.$id.'
	');
	
	/*echo '<pre>';
	print_r($affected);exit;*/
	return $affected[0]->keywords;
}

function getSettingAdName($id){
	$affected = DB::select('
		SELECT
		*
		FROM
		ads
		WHERE
		ad_id = '.$id.'
	');
	
	/*echo '<pre>';
	print_r($affected);exit;*/
	return $affected[0]->ad_name;
}

function getSettingKeywords($id){
	$affected = DB::select('
		SELECT
		*
		FROM
		adkeyword
		WHERE
		adgroup_id = '.$id.'
	');
	$html = '';
	if(count($affected)>0){
		$html .= '<li style="height:25px !important; min-height:25px;" ><a class="childswords" href="javascript:void(0);" data-id="0" data-val="0">Select Keyword</a></li>';
		foreach($affected as $row){
			$html .= '<li style="height:25px !important; min-height:25px;" ><a href="javascript:void(0);" data-id="'.$row->keyword_id .'"  data-aid="'.$row->account_id.'"  data-c="'.$row->campaign_id.'"  data-account="'.$row->caccount_id.'" class="childswords">'.$row->keywords.'</a></li>';
			
		}
	}
	return $html;
}

function getSettingAds($id){
	$affected = DB::select('
		SELECT
		*
		FROM
		ads
		WHERE
		adgroup_id = '.$id.'
	');
	$html = '';
	if(count($affected)>0){
		$html .= '<li style="height:25px !important; min-height:25px;" ><a class="childsAds" href="javascript:void(0);" data-id="0" data-val="0">Select Ad</a></li>';
		foreach($affected as $row){
			$html .= '<li style="height:25px !important; min-height:25px;" ><a href="javascript:void(0);" data-id="'.$row->ad_id .'"  data-aid="'.$row->account_id.'"  data-c="'.$row->campaign_id.'"  data-account="'.$row->caccount_id.'" class="childsAds">'.$row->ad_name.'</a></li>';
			
		}
	}
	return $html;
}

function dateValue($d){
	return str_replace('_',' ',$d);
}

function getAccountCpcH($id,$ClientID,$f){
	if($f == 'Cpa'){
		$f = 'CostPerAllConversion';
	}
	//echo $id.'>>>'.$ClientID.'>>>'.$f;exit;
	$affected = DB::select('
		SELECT
		*
		FROM
		accounts
		WHERE
		id = '.$id.'
	');
	$token = $affected[0]->token;
	$user = new AdWordsUser();
	PrevInitAdwordsUserSettings($user);
	$cpc = 0;
	if(LoadAdWordsAccessTokenForClient($user, $token) ){ 
		$user->SetClientCustomerId($ClientID); 
		$reportQuery = 'SELECT '.$f.' FROM ACCOUNT_PERFORMANCE_REPORT '
      . 'WHERE ExternalCustomerId = '.$ClientID.'';

		$options = array('version' => ADWORDS_VERSION);
		$name = time().'.csv';
		$filePath = $_SERVER['DOCUMENT_ROOT'].'/admetric/csv/'.$name;
		$reportFormat = 'CSV';
		ReportUtils::DownloadReportWithAwql($reportQuery, $filePath, $user,
		$reportFormat, $options);
		$file = fopen($filePath,"r");
		$i=1;
		
		while(!feof($file))
		  {
			 $cpcArray = fgetcsv($file);
		  	if($i==3){
				$cpc = $cpcArray[0];
			}
			if($i>3){
				break;
			}
			$i++;
		  }

		fclose($file);
		unlink($filePath);
		
		}
	return $cpc;
}

function getSettingCampaignCPC($id){
	$affected = DB::select('
		SELECT
		*
		FROM
		campaign
		WHERE
		campaign_id = '.$id.'
	');
	
	/*echo '<pre>';
	print_r($affected);exit;*/
	return $affected[0]->cpc;
}

function getAllSettingCPC($id){
	$affected = DB::select('
		SELECT
		*
		FROM
		metrics
		WHERE
		id = '.$id.'
	');
	$request = $affected[0];
	$id = $request->id;
	$account = $request->set_aacount;
	$campaign = $request->set_campaign;
	$adgroup = $request->set_adgroup;
	$keywords = $request->set_keyword;
	$ads = $request->set_ad;
	$report = $request->report;
	$date = $request->date_time;
	$type = $request->date_type;
	$affected = DB::select('
		SELECT
		*
		FROM
		accounts
		WHERE
		id = '.$request->account_id.'
	');
	$token = $affected[0]->token;
	$cpc = 0;
	$where = '';
	$dateRange = '';
	if($type != '' && $date != ''){
		if($type == 'middle'){
			$d = explode('-',$date);
			$dateRange = ' DURING '.sprintf('%d,%d',
			date('Ymd', strtotime($d[0])), date('Ymd', strtotime($d[1])));
		}else if($type == 'lower'){
			$d = explode('-',$date);
			$dateRange = ' DURING '.sprintf('%d,%d',
			date('Ymd', strtotime('-'.$date.' day')), date('Ymd', strtotime('-1 day')));
		}else{
			if($date == 'LAST_3_MONTHS'){
				$d = strtotime(' -4 months');
				$n =  date('Y/m',$d).'/1';
				$dateRange = ' DURING '.sprintf('%d,%d',
				date('Ymd', strtotime($n)), date('Ymd', strtotime($n.' +90 day')));
			}else if($date == 'LAST_YEAR'){
				$d = strtotime(' -1 year');
				$n =  date('Y',$d).'/1/1';
				$dateRange = ' DURING '.sprintf('%d,%d',
				date('Ymd', strtotime($n)), date('Ymd', strtotime($n.' +12 months')));
			}else if($date == 'ALL_TIME'){
				$dateRange = '';
			}else{
				$dateRange = ' DURING '.$date;
			}
		}
	}
	
	if($account > 0 && $campaign == 0 && $adgroup == 0 && $keywords == 0 && $ads == 0){
		$where = 'ExternalCustomerId';
		$cpc = getSettingCpc($token,$account,0,$where,$report,$dateRange,$request->metric_id);
	}else if ($account > 0 && $campaign > 0 && $adgroup == 0 && $keywords == 0 && $ads == 0){
		$where = 'CampaignId';
		$cpc = getSettingCpc($token,$account,$campaign,$where,$report,$dateRange,$request->metric_id);
	}else if ($account > 0 && $campaign > 0 && $adgroup > 0 && $keywords == 0 && $ads == 0){
		$where = 'AdGroupId';
		$cpc = getSettingCpc($token,$account,$adgroup,$where,$report,$dateRange,$request->metric_id);
	}else if ($account > 0 && $campaign > 0 && $adgroup > 0 && $keywords > 0 && $ads == 0){
		$where = 'Id';
		$cpc = getSettingCpc($token,$account,$keywords,$where,$report,$dateRange,$request->metric_id);
	}else if ($account > 0 && $campaign > 0 && $adgroup > 0 && $keywords == 0 && $ads > 0){
		$where = 'Id';
		$cpc = getSettingCpc($token,$account,$ads,$where,$report,$dateRange,$request->metric_id);
	}
	//echo '<pre>';
	//echo $account.'>>>'.$campaign.'>>>'.$adgroup.'>>>'.$keywords.'>>>'.$ads.'>>>'.$where.'>>>'.$report.'>>>'.$dateRange;exit;
	//print_r($affected);exit;
	return $cpc;
}

function getSettingCpcDate($token,$ClientID,$id = 0,$where,$report,$dateRange,$f){
	
	if($f == 'Cpa'){
		$f = 'CostPerAllConversion';
	}
	$f .=',Date';
	$user = new AdWordsUser();
	PrevInitAdwordsUserSettings($user);
	$cpc = 0;
	if(LoadAdWordsAccessTokenForClient($user, $token) ){ 
		$user->SetClientCustomerId($ClientID); 
		$reportQuery = 'SELECT '.$f.' FROM '.$report.' WHERE ';
		
		if($id == 0){
			$reportQuery .= ''.$where.' = '.$ClientID.'';
		}else{
			$reportQuery .= ''.$where.' = '.$id.'';
		}
		
		if($dateRange != ''){
			$reportQuery .= $dateRange;
		}else{
			$d = strtotime(' -12 months');
			$n =  date('Y/m/d',$d);
			$dateRange = ' DURING '.sprintf('%d,%d',
			date('Ymd', strtotime($n)), date('Ymd', time()));
			$reportQuery .= $dateRange;
		}
		//echo $reportQuery;exit;
		$options = array('version' => ADWORDS_VERSION);
		$name = time().'.csv';
		$filePath = $_SERVER['DOCUMENT_ROOT'].'/admetric/csv/'.$name;
		$reportFormat = 'CSV';
		$filePath = NULL;
		$reportData = ReportUtils::DownloadReportWithAwql($reportQuery, $filePath, $user,
		$reportFormat, $options);
		$Data = str_getcsv($reportData, "\n");
		
		
		$dates = explode(' ',$dateRange);
		
		$dates = $dates[2];
		$dateSprate = explode(',',$dates);
		/*echo '<pre>';
		print_r($dateSprate);exit;*/
		if(count($dateSprate) == 2){
			$d1 = date('Y-m-d',strtotime($dateSprate[0]));
			$d2 = date('Y-m-d',strtotime($dateSprate[1]));
		}else{
			$s = explode('_',$dateSprate[0]);
			$datess = $s[1];
			$t = date('Y-m-d',strtotime(date('Y-m-d').' -1 day'));
			$d1 = date('Y/m/d', strtotime($t.' -'.($datess).' day'));
			$d2 = date('Y/m/d', strtotime($d1.' +'.($datess).' days'));
		}
		$date1 = new DateTime($d1);
		$date2 = new DateTime($d2);
		$difference = $date1->diff($date2);
		$d = $difference->format('%a');
		$dateArray = array();
		if(count($dateSprate) == 2){
			$dateArray[] = $d2;
		}
		for($i=1;$i<=$d;$i++){
		 $dateArray[] = date('Y-m-d',strtotime($d1.' +'.$i.' days')); 
		}
		$dateArray2 = array();
		for($i=0;$i<count($Data);$i++){
			if($i>1 && $i<(count($Data)-1)){
				$row = explode(',',$Data[$i]);
				if(!in_array($row[1],$dateArray2)){
					$dateArray2[] = $row[1];
				}
				
			}
		}
		
		$resultArray = array();
		if(count($Data)>3){
			for($j=0;$j<count($dateArray);$j++){
				$total = 0;
				$oneArray = array();
				if(in_array($dateArray[$j],$dateArray2)){
					for($i=0;$i<count($Data);$i++){
						if($i>1 && $i<(count($Data)-1)){
							$row = NULL;
							$row = explode(',',$Data[$i]);
							if($row[1] == $dateArray[$j]){
								$total = floatval($row[0]);
								$oneArray['v'] = number_format(($total),2);
								$oneArray['d'] = $dateArray[$j];
							}
							
						}
					}
				}else{
					$oneArray['v'] = number_format(($total),2);
					$oneArray['d'] = $dateArray[$j];
				}
				$resultArray[] =$oneArray;
			}
		}else{
			for($j=0;$j<count($dateArray);$j++){
				$total = 0;
				$oneArray = array();
				$oneArray['v'] = number_format(($total),2);
				$oneArray['d'] = $dateArray[$j];
				$resultArray[] =$oneArray;
			}
		}
		
		
	}
	
	return $resultArray;
}

function getDashbordName($id){
	$affected = DB::select('
		SELECT
		name
		FROM
		dashboard
		WHERE
		id = '.$id.'
	');
	if(count($affected)>0){
		$affected = $affected[0]->name;
	}else{
		$affected = 'Select Dashboard';
	}
	return $affected;
}

function checkPackage($userId){
	$userPackage = DB::select('
				SELECT
				u.package_id,
				u.package_start_date,
				p.*
				FROM
				user_package u
				JOIN
				package p
				ON
				p.id = u.package_id
				WHERE
				u.user_id = '.$userId.'
			');
			if(count($userPackage)>0){
				$userPackage = $userPackage[0];
				$currentDate = date('Y-m-d');
				$expireDate = date('Y-m-d',strtotime($userPackage->package_start_date.' +'.$userPackage->days.' days'));
				if(strtotime($currentDate)>strtotime($expireDate)){
					return false;
				}else{
					return true;
				}
				
			}
}

function remaingDays($userId){
	$userPackage = DB::select('
				SELECT
				u.package_id,
				u.package_start_date,
				p.*
				FROM
				user_package u
				JOIN
				package p
				ON
				p.id = u.package_id
				WHERE
				u.user_id = '.$userId.'
			');
			if(count($userPackage)>0){
				$userPackage = $userPackage[0];
				$currentDate = date('Y-m-d');
				$expireDate = date('Y-m-d',strtotime($userPackage->package_start_date.' +'.$userPackage->days.' days'));
				if(strtotime($currentDate)>strtotime($expireDate)){
					return $userPackage->title.' ( Remaining Days)';
				}else{
					$date1 = new DateTime($currentDate);
					$date2 = new DateTime($expireDate);
					$diff = $date1->diff($date2)->format("%a");
					return $userPackage->title.' ( Remaining Days '.$diff.')';
				}
				
			}else{
				return false;
			}
}

function get30Days(){
	$current = date('Y-m-d');
	$lastDay = date('Ymd',strtotime($current.' -1 days'));
	$last30Days = date('Ymd',strtotime($current.' -30 days'));
	$reqDate = $last30Days.','.$lastDay;
	return $reqDate;
}

function conDate($date){
	 $date = explode(',',$date);
	 $d1 = date('M d, Y',strtotime($date[0]));
	 $d2 = date('M d, Y',strtotime($date[1]));
	 return $d1.' - '.$d2;	
}




