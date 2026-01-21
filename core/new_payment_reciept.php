<?php
//https://papis.tao.ai/core.content.get?
//mod=core&token=QDd8INz6&ops=receipt&type=receipt&receipt_id=3e159952a68daed8f3095cd659351c11
//&cache%5Bname%5D=core_3ca9d306&source=http%3A%2F%2Flocalhost%2Fhires-i&sub_secret_token=569d89ab
$valid_dir_viewer  = 0;
if(taoh_user_is_logged_in()){
    $data = taoh_session_get(TAOH_ROOT_PATH_HASH)['USER_INFO'];
    $ptoken = $user_ptoken = $data->ptoken;
    $valid_dir_viewer  = 1;
    if(!$data->profile_complete){
        $valid_dir_viewer  = 0;
    }
}else{
    //taoh_redirect( TAOH_SITE_URL_ROOT ); taoh_exit();
}
//$valid_dir_viewer  = 0;

//echo "============".$valid_dir_viewer;die();

//echo "==========".taoh_parse_url(1);die();

//https://ppapi.tao.ai/core.content.get?mod=core&token=QDd8INz6&ops=receipt&receipt_id=sdasdsadsad
//&cache%5Bname%5D=receipt_65c2fbec&source=http%3A%2F%2Flocalhost%2Fhires-i&sub_secret_token=569d89ab
if(!taoh_parse_url(1)){
    taoh_redirect( TAOH_SITE_URL_ROOT ); taoh_exit();
}
$receipt_id = taoh_parse_url(1);
$taoh_call = "core.content.get";
$taoh_vals = array(    
    'mod'=>'core',
    'token'=>taoh_get_dummy_token(1),
    'ops'=>'receipt',
    'type'=>'receipt',
    'receipt_id' =>$receipt_id,
     //'cfcc1d' => 1 //cfcache newly added
);
//$cache_name = $taoh_call.'_receipt_' . hash('sha256', $taoh_call . serialize($taoh_vals));
$cache_name = $taoh_call.'_receipt_'.$receipt_id;

//$taoh_vals[ 'cfcache' ] = $cache_name;
$taoh_vals[ 'cache_name' ] = $cache_name;
ksort($taoh_vals);


//echo taoh_apicall_get_debug($taoh_call, $taoh_vals);exit();
$receipt = json_decode( taoh_apicall_get($taoh_call, $taoh_vals), true );

//echo'<pre>';print_r($receipt);die();
if($receipt['success'] == false){
 taoh_redirect( TAOH_SITE_URL_ROOT ); taoh_exit();
}
$receipt_data = $receipt['output'];
$purchase = json_decode($receipt_data['meta']['purchase_details'],1);
$customer_details = json_decode($receipt_data['meta']['purchase_info'],1);
$customer_name = $customer_details['customer_name'];
$customer_email = $customer_details['customer_email'];

$total = $purchase['total_price'];
$amt = $purchase['net_total'];
$currency = $purchase['currency'];


if(isset($receipt_data['receipt_info'])){
    $org_name = $receipt_data['receipt_info']['company_name'];
	$org_add_1 = $receipt_data['receipt_info']['company_address_1'];
    $org_address = $receipt_data['receipt_info']['company_address_1'].'<br> '.
                    $receipt_data['receipt_info']['company_address_2'].'<br> '.
                    $receipt_data['receipt_info']['company_city'].'<br> '.
                    $receipt_data['receipt_info']['company_state'].'<br> '.
                    $receipt_data['receipt_info']['company_zip'].'<br> '.
                    $receipt_data['receipt_info']['company_country'];
    $org_tax_id = $receipt_data['receipt_info']['company_taxid'];
    $is_company_reg = $receipt_data['receipt_info']['registered_company'];
}else{
    $org_name = $receipt_data['pod']['TAOH_ORG_NAME']?$receipt_data['pod']['TAOH_ORG_NAME']:TAOH_SITE_NAME_SLUG;
    $org_add_1 = $org_address = $receipt_data['pod']['TAOH_ORG_ADDRESS'];
    $org_tax_id = $receipt_data['pod']['TAOH_TAX_ID'];
    $is_company_reg = $receipt_data['pod']['TAOH_COMPANY_REG'];
}

$org_phone = $receipt_data['pod']['TAOH_ORG_PHONE'];
$org_site = $receipt_data['pod']['TAOH_SITEURL'];
if(!isset($org_site) || $org_site == ''){
    $s_site = json_decode($receipt_data['meta']['sitesource'],1);
    $org_site = $s_site['site_source'];
}
$is_profit = $receipt_data['pod']['TAOH_ORG_TYPE'];
$community_org_email   = $receipt_data['pod']['TAOH_COMMUNITY_ORG_EMAIL'];
$admin_email  = $receipt_data['pod']['TAOH_SUPERADMIN_MAIL'];
$event_details = array();
$event_owner_details = array();
if($receipt_data['app_slug'] == 'rsvp' || $receipt_data['app_slug'] == 'sponsor'){
    $event_details = json_decode($receipt_data['meta']['event_details'],1);
    $event_owner_details = $receipt_data['owner'];
    

}


//echo '<pre>';print_r($receipt_data);
//echo '<pre>';print_r($event_details);die();


$order = explode('_',$receipt_data['order_ID']);

$order_conbt = count($order);
$order_id = $order[$order_conbt-1];

taoh_get_header(); 
?>

<style>
    .payment-receipt {
        color: #000000;
        font-size: 16px;
    }
    .payment-receipt .heading {
        font-size: 21px;
        line-height: 24px;
        font-weight: 700;
        color: #000000;
    }
    .payment-receipt .sub-heading {
        font-size: 16px;
        line-height: 24px;
        font-weight: 700;
        color: #000000;
    }
    .payment-receipt .heading.underline {
        border-bottom: 1.5px solid #000000;
        width: fit-content;
        padding-bottom: 4px;
    }
   
    .details-con * {
        font-size: 15px;
        font-weight: 400;
        line-height: 24px;
        color: #000000;
    }
    .payment-receipt .font-weight-bold {
        font-weight: 700;
    }
    .details-con .receiver-detail h4, .details-con .donor-detail h4{
        font-weight: 700;
    }
    .payment-receipt .b-b-grey {
        border-bottom: 1.5px solid #d3d3d3;
    }

    .label-bg {
        background: #d9d9d9;
        font-size: 15px;
        font-weight: 500;
        color: #000000;
        padding: 6px 10px;
    }
    .result-value {
        font-size: 15px;
        font-weight: 500;
        color: #444444;
    }
    .i-text {
        font-size: 14px;
        font-weight: 300;
        font-style: italic;
        line-height: 24px;
        color: #000000;
    }
    .payment-receipt img.signature {
        width: 100%;
        max-width: 151px;
    }

    table {
       width: 100%;
       border: 1px solid #d3d3d3;
       border-radius: 6px;
       padding: 17px 17px;
    }
    table td {
        width: 33%;
        font-size: 15px;
        font-weight: 500;
        line-height: 19px;
    }
    table th {
        line-height: 19px;
    }
    .b-t-none {
        border-top: 0 !important;
    }
    .b-r {
        border-right: 1px solid #d3d3d3;
    }  
</style>

    <div style="background-color: #ffffff;" class="<?php echo (!$valid_dir_viewer ? 'lblur' : ''); ?>">
        
        <div class="container payment-receipt bg-white py-5">
            <h3 class="heading underline">
                Payment Receipt <?php echo $receipt_data['meta']['payment_status'];?>
            </h3>

            <div class="details-con row mt-5">
                <div class="col-lg-11 row flex-wrap-reverse">
                    <div class="col-lg-8 row d-flex justify-content-between">
                        <div class="col-md-6 col-lg-4 receiver-detail mb-3">
                            <?php if($receipt_data['app_slug'] == 'rsvp' || $receipt_data['app_slug'] == 'sponsor'){

                                $event_owner_name = $event_owner_details['fname'].' '.$event_owner_details['lname'];
                                $event_owner_address = $event_owner_details['full_location'];
                              
                            
                            ?>
                                <h4 class="font-weight-bold mb-2">Event Owner(Receiver) Details</h4>
                                <div class="r-content">
                                <p><?php echo ucfirst($org_name);?></p> 
                                <p><span><?php echo $org_address;?></span></p>
                                   
                                <?php if($org_tax_id) { ?>
                                        <p><span class="font-weight-bold">Tax ID : </span> <?php echo $org_tax_id;?></p>
                                    <?php } ?>
                                </div>
                            <?php } else { ?>
                                <h4 class="font-weight-bold mb-2">Receiver Details</h4>
                                <div class="r-content">
									<?php if($org_name) { ?>
										<p><?php echo ucfirst($org_name);?></p> 
									<?php } ?>
									
									<?php if($org_add_1) { ?>
										<p><span><?php echo $org_address;?></span></p>
									<?php } ?>
                                    <p><?php echo $org_site;?></p>
									
                                    <p><?php if($community_org_email) echo $community_org_email;
                                            else echo $admin_email;?></p>
                                    <p><?php echo $org_phone;?></p>
                                    <?php if($org_tax_id) { ?>
                                        <p><span class="font-weight-bold">Tax ID : </span> <?php echo $org_tax_id;?></p>
                                    <?php } ?>
                                
                                    <p><!-- ---<span class="font-weight-bold">IRS Tax-Exempt Status : </span>-->
                                    <?php if($is_profit) { ?>
                                        <span style="font-weight: 500;">Nonprofit Organization</span>
                                    <?PHP } ?>
                                    </p>
                                </div>
                            <?php } ?>
                        </div>
                        <?php if($receipt_data['user'] ) { ?>
                            <div class="col-md-6 col-lg-4 donor-detail mb-3">
                                <h4 class="font-weight-bold mb-2">
                                    <?php 
                                    if($receipt_data['app_slug'] == 'donate') echo 'Donor Details';
                                    else if($receipt_data['app_slug'] == 'jobchat') echo 'Purchased By';
                                    else if($receipt_data['app_slug'] == 'rsvp') echo 'Purchased By';
                                    else if($receipt_data['app_slug'] == 'sponsor') echo 'Purchased By';
                                    else  echo 'Paid by';
                                    ?>
                                    </h4>
                                <div class="r-content">
                                    <p><?php echo $receipt_data['user']['fname']. ' ' .$receipt_data['user']['lname'];?></p>
                                    <span><?php echo $receipt_data['user']['full_location'];?></span> 
                                    <p><?php echo $customer_email;?></p>
                                </div>
                            </div>
                        <?php } ?>
                    </div>
                    <div class="col-lg-4 basic-detail d-flex justify-content-end mb-3">
                        <div>
                            <p class="mb-1"><span class="font-weight-bold">Date : </span><?php echo date('M d, Y',$receipt_data['created']);?></p>
                            <p class="mb-1"><span class="font-weight-bold"> Order Number : </span><?php echo $order_id;?> - <?php echo $receipt_data['meta']['payment_status'];?></p>
                            <p class="font-weight-bold mb-1">Transaction ID : <?php echo $receipt_data['transaction_id'];?></p>
                            <p class="font-weight-bold mb-1">Payment Method : Online <?php if($receipt_data['meta']['payment_method'] !='link') echo '/ '.$receipt_data['meta']['payment_method'];?></p>
                        </div>
                    </div>
                </div>
            </div>
            <?php if($receipt_data['app_slug'] == 'donate') {  ?>
            <div class="mt-4">
                <h3 class="heading b-b-grey pb-3">Particulars towards Donation to <?php echo ucfirst($org_name);?></h3>
                <div class="row mx-0">
                    <div class="col-12 row mx-0 d-flex justify-content-end b-b-grey pb-2 pt-4 pb-3 px-0">
                        <div class="col-lg-6 col-xl-5 px-0">
                            <span class="label-bg mr-3">Amount/Value</span>
                            <span class="result-value">$<?php echo $amt;?></span>
                        </div>
                    </div>
                    <div class="col-12 row mx-0 d-flex justify-content-end b-b-grey pb-2 pt-4 pb-3 px-0">
                        <div class="col-lg-6 col-xl-5 px-0">
                            <span class="label-bg mr-3">Payment Mode</span>
                            <span class="result-value">Online Payment <?php if($receipt_data['meta']['payment_method'] !='link') echo '/ '.$receipt_data['meta']['payment_method'];?></span>
                        </div>
                    </div>
                </div>
            </div>
            <?php } ?>
            <?php if($receipt_data['app_slug'] == 'job') {  ?>
            <div class="mt-5">
                <h3 class="sub-heading">Purchase Details</h3>
                <!-- for medium size screens -->
                 <table class="table d-none d-md-block mt-3">
                    <thead>
                        <tr>
                            <th class="b-t-none">Particulars</th>
                            <th class="b-t-none">Price per unit</th>
                            <th class="b-t-none">Qty</th>
                            <th class="b-t-none">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(isset($purchase['item'])) {
                            //echo '<pre>';print_r($purchase['item']);die();
                            foreach($purchase['item'] as $key=>$val){
                                $c = $total;
                                if(isset($val['price']) && $val['price'] > 0){
                                    $c = $count * $val['price'];   
                                }
                        ?>
                            <tr>
                                <td class="py-4"><?php echo $val['product_name'];?></td>
                                <td class="py-4">$<?php echo $c;?></td>
                                <td class="py-4"><?php echo $val['count'];?></td>
                                <td class="py-4">$<?php echo $amt;?></td>
                            </tr>
                        <?php
                            }
                        }
                        ?>
                        <tr>
                            <td colspan="4" class="text-right">
                                <p>Total: $<?php echo $total;?></p>
                                <p>Online <?php if($receipt_data['meta']['payment_method'] !='link') echo '/ '.$receipt_data['meta']['payment_method'];?></p>
                            </td>
                        </tr>
                    </tbody>
                 </table>
                <!-- for small size screens -->
                 <table class="table d-block d-md-none mt-3">
                    <tbody>
                        <tr>
                            <th class="py-4 w-50 b-t-none b-r">Particulars</th>
                            <td class="py-4 w-50 b-t-none">Token Purchase for 
                            posting jobs.</td>
                        </tr>
                        <tr>
                            <th class="py-4 w-50 b-r">Price per unit</th>
                            <td class="py-4 w-50">$<?php echo $total;?></td>
                        </tr>
                        <tr>
                            <th class="py-4 w-50 b-r">Qty</th>
                            <td class="py-4 w-50">01</td>
                        </tr>
                        <tr>
                            <th class="py-4 w-50 b-r">Subtotal</th>
                            <td class="py-4 w-50">$<?php echo $amt;?></td>
                        </tr>
                        <tr>
                            <td colspan="2" class="text-right">
                                <p>Total: $<?php echo $total;?></p>
                                <p>Online <?php if($receipt_data['meta']['payment_method'] !='link') echo '/ '.$receipt_data['meta']['payment_method'];?></p>
                            </td>
                        </tr>
                    </tbody>
                 </table>
            </div>
            <?php } ?>
            <?php if($receipt_data['app_slug'] == 'rsvp') {  ?>
            <div class="mt-5">
                <h3 class="sub-heading">Purchase Details</h3>
                <!-- for medium size screens -->
                 <table class="table d-none d-md-block mt-3">
                    <thead>
                        <tr>
                            <th class="b-t-none">Particulars</th>
                            <th class="b-t-none">Price per unit</th>
                            <th class="b-t-none">Qty</th>
                            <th class="b-t-none">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="py-4">
                                <p><?php echo $event_details['eventtitle'] ;?></p> 
                                <p style="color: #7C7C7C; font-weight: 500;">Ticket Type: <?php echo $event_details['tokentyp'] ;?></p>
                            </td>
                            <td class="py-4">$<?php echo $total;?></td>
                            <td class="py-4">01</td>
                            <td class="py-4">$<?php echo $amt;?></td>
                        </tr>
                        <tr>
                            <td colspan="4" class="text-right">
                                <p>Total: Online Payment <?php if($receipt_data['meta']['payment_method'] !='link') echo '/ '.$receipt_data['meta']['payment_method'];?></p>
                                
                            </td>
                        </tr>
                    </tbody>
                 </table>
                <!-- for small size screens -->
                 <table class="table d-block d-md-none mt-3">
                    <tbody>
                        <tr>
                            <th class="py-4 w-50 b-t-none b-r">Particulars</th>
                            <td class="py-4 w-50 b-t-none">
                            <p><?php echo $event_details['eventtitle'] ;?></p> 
                                <p style="color: #7C7C7C; font-weight: 500;">Ticket Type: <?php echo $event_details['tokentyp'] ;?></p>
                            </td>
                        </tr>
                        <tr>
                            <th class="py-4 w-50 b-r">Price per unit</th>
                            <td class="py-4 w-50">$<?php echo $total;?></td>
                        </tr>
                        <tr>
                            <th class="py-4 w-50 b-r">Qty</th>
                            <td class="py-4 w-50">01</td>
                        </tr>
                        <tr>
                            <th class="py-4 w-50 b-r">Subtotal</th>
                            <td class="py-4 w-50">$<?php echo $amt;?></td>
                        </tr>
                        <tr>
                            <td colspan="2" class="text-right">
                                <p>Total: $<?php echo $total;?></p>
                                <p>Online Payment/ <?php if($receipt_data['meta']['payment_method'] !='link') echo '/ '.$receipt_data['meta']['payment_method'];?></p>
                            </td>
                        </tr>
                    </tbody>
                 </table>
            </div>
            <?php } ?>
            <?php if($receipt_data['app_slug'] == 'sponsor') {  ?>
            <div class="mt-5">
                <h3 class="sub-heading">Purchase Details</h3>
                <!-- for medium size screens -->
                 <table class="table d-none d-md-block mt-3">
                    <thead>
                        <tr>
                            <th class="b-t-none">Particulars</th>
                            <th class="b-t-none">Price per unit</th>
                            <th class="b-t-none">Qty</th>
                            <th class="b-t-none">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="py-4">
                                <p>Sponsorship For <?php echo $event_details['eventtitle'] ;?></p> 
                                <p style="color: #7C7C7C; font-weight: 500;">Sponsorship Level: <?php echo isset($event_details['level_title']) ? $event_details['level_title'] : $event_details['display_type'] ;?></p>
                            </td>
                            <td class="py-4">$<?php echo $amt;?></td>
                            <td class="py-4">01</td>
                            <td class="py-4">$<?php echo $amt;?></td>
                        </tr>
                        <tr>
                            <td colspan="4" class="text-right">
                                <p>Total: $<?php echo $total;?></p>
                                <p>Online <?php if($receipt_data['meta']['payment_method'] !='link') echo '/ '.$receipt_data['meta']['payment_method'];?></p>
                            </td>
                        </tr>
                    </tbody>
                 </table>
                <!-- for small size screens -->
                 <table class="table d-block d-md-none mt-3">
                    <tbody>
                        <tr>
                            <th class="py-4 w-50 b-t-none b-r">Particulars</th>
                            <td class="py-4 w-50 b-t-none">
                                <p>Sponsorship For <?php echo $event_details['eventtitle'] ;?></p> 
                                <p style="color: #7C7C7C; font-weight: 500;">Sponsorship Level: <?php echo $event_details['title'] ;?></p>
                            </td>
                        </tr>
                        <tr>
                            <th class="py-4 w-50 b-r">Price per unit</th>
                            <td class="py-4 w-50">$<?php echo $amt;?></td>
                        </tr>
                        <tr>
                            <th class="py-4 w-50 b-r">Qty</th>
                            <td class="py-4 w-50">01</td>
                        </tr>
                        <tr>
                            <th class="py-4 w-50 b-r">Subtotal</th>
                            <td class="py-4 w-50">$<?php echo $amt;?></td>
                        </tr>
                        <tr>
                            <td colspan="2" class="text-right">
                                <p>Total: $<?php echo $total;?></p>
                                <p>Online <?php if($receipt_data['meta']['payment_method'] !='link') echo '/ '.$receipt_data['meta']['payment_method'];?></p>
                            </td>
                        </tr>
                    </tbody>
                 </table>
            </div>
            <?php } ?>

            <div class="mt-5 mb-4 row">
                <div class="col-lg-6 ">
                    <h3 class="sub-heading mb-3">Statement of Goods and Services</h3>
                    <p class="i-text">This receipt acknowledges that <span class="font-weight-bold "><?php echo ucfirst($org_name);?></span> did not provide any goods or services in return for this contribution, other than intangible religious benefits (if applicable).</p>
                </div>
            </div>
            <?php if(!$is_profit) { ?>
            <div class="mb-4 row">
                <div class="col-lg-6">
                    <h3 class="sub-heading mb-3">Organization Statement</h3>
                    <p class="i-text"><span class="font-weight-bold "><?php echo ucfirst($org_name);?></span> is a tax-exempt organization under Section 501(c)(3) of the Internal Revenue Code. Contributions are tax-deductible to the extent allowed by law. Please retain this receipt for your tax records</p>
                </div>
            </div>
            <?php } else { ?>
            <div class="mb-4 row">
                <div class="col-lg-6">
                    <h3 class="sub-heading mb-3">Organization Statement For Profit</h3>
                    <p class="i-text">Please note that <span class="font-weight-bold "><?php echo ucfirst($org_name);?></span> is a for-profit business/DBA. Payments made are not tax-deductible and are for the purchase of goods or services as outlined above. Once again, thank you for your support. 
                    If you have any questions or need further assistance, please feel free to contact us at <?php echo ucfirst($org_phone);?>.</p>
                </div>
            </div>
            <?php } ?>

            <div class="row mt-5">
                <p class="i-text text-center col-lg-11 px-lg-4 mx-auto ">
                    This is a computer generated receipt, This doesn't require a signature. 
                    We recommend consulting your tax advisor for specific guidance regarding your contribution. 
                    Once again, thank you for your support. If you have any questions or need further assistance, 
                    <?php 
                    $e_mail  = $community_org_email ? $community_org_email :  $admin_email; ?>
                    please feel free to contact us at <a href="mailTo:<?php echo $e_mail; ?>" ><?php echo $e_mail; ?> </a>
                                        
                                        <?php if($org_phone) echo 'or'.$org_phone;?>.</p>
            </div>
            
        </div>
    </div>



<?php
if (!taoh_user_is_logged_in()) {
    echo '<div class="col footer-prompt" id="login-prompt" >';
    echo '<h5 class="pb-2">You need to log in to see the full content!</h5>';
    echo '<a href="' . (TAOH_LOGIN_URL ?? '') . '" class="btn theme-btn" id="login-btn"><i class="la la-sign-in mr-1"></i>Login / Signup</a>';
    echo '</div>';
}
if (!$valid_dir_viewer) {
    if (taoh_user_is_logged_in()) {
        echo '<div class="col footer-prompt">';
        echo '<h5 class="pb-2">Complete your settings to fully use the platform.</h5>';
        echo '<a href="' . (TAOH_SETTINGS_URL ?? '') . '" class="btn theme-btn" id="login-btn"><i class="la la-cog mr-1"></i>Complete Settings</a>';
        echo '</div>';
    }
}
taoh_get_footer();
?>