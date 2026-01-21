<?php

$form_field = array(
			"header"=>array('image_link','header_image','img','0','common'),
			"form_fields"=>
				array(
					array(
						'label' => 'First Name',
						'name' => 'fname',
						'type'=>'text',
						'required'=>'1',
						'place'=>'common'),
					array(
						'label' => 'Last Name',
						'name' => 'lname',
						'type'=>'text',
						'required'=>'1',
						'place'=>'common'),
					array(
						'label' => 'Email',
						'name' => 'email',
						'type'=>'email',
						'required'=>'1',
						'place'=>'common'),
					array(
						'label' => 'Organization Name',
						'name' => 'org',
						'type'=>'text',
						'required'=>'1',
						'place'=>'common'),
					array(
						'label' => 'Role / Title',
						'name' => 'role',
						'type'=>'text',
						'required'=>'1',
						'place'=>'common'),
					array(
						'label' => 'Sponsor', 
						'name' => 'sponser',
						'required'=>'1',
						'type'=>'question',
						'place'=>'main',		
						'questions' => array( 
							array(
								'label' => 'Please describe your organization`s main area of interest or focus for sponsorship.',
								'name' => 'organization_area',
								'type'=>'text',
								'required'=>'0',
								'place'=>'main') 
							)),
					array(
						'label' => 'Partner', 
						'name' => 'partner',
						'required'=>'1',
						'type'=>'question',
						'place'=>'main',					
						'questions' => array( 
							array(
								'label' => 'What type of partnership are you interested in? (E.g., content, Talent, marketing, technology)',
								'name' => 'interested_partnership',
								'type'=>'text',
								'required'=>'0',
								'place'=>'main') 
						)),
					array(
						'label' => 'Volunteer', 
						'name' => 'volunteer', 
						'required'=>'1',
						'type'=>'question',
						'place'=>'main',
						'questions' => array( 
							array(
								'label' => 'What areas are you interested in volunteering for? (E.g., social media and marketing)',
								'name' => 'volunteer_interested_area',
								'type'=>'text',
								'required'=>'0',
								'place'=>'main'), 
							array(
								'label' => 'Do you have previous experience in these areas?',
								'name' => 'previous_experience_area',
								'type'=>'radio',
								'required'=>'0',
								'place'=>'main',
								'option'=>array('label'=>'Yes', 'name'=>'yes','label'=>'No','name'=>'no') ), 
							array(
								'label' => 'If Yes, please describe.',
								'name' => 'experience_describe',
								'type'=>'textarea',
								'required'=>'0',
								'place'=>'main'),
						)),
					
					array(
							'label' => 'I am interested in being a',
							'name' => 'interested',
							'type'=>'radio',
							'required'=>'1',
							'place'=>'workcongress24',
							'options' => 
                                    array(
                                        array('label'=>'Sponsor', 'name'=>'sponsor'),
                                        array('label'=>'Partner', 'name'=>'partner'),
                                        array('label'=>'Host Local', 'name'=>'localhost'),
                                        array('label'=>'Speaker', 'name'=>'speaker'),
                                        array('label'=>'Participant', 'name'=>'participant'),
                                        array('label'=>'Volunteer', 'name'=>'volunteer'),

                                        
							        ),
							'actions'=>array(
								'sponsor' => array(
									array(
										'label' => 'What is your primary objective for sponsorship?',
										'name' => 'sponsorship',
										'type'=>'checkbox',
										'required'=>'1',
										'place'=>'workcongress24', 
										'option' => array(
                                            array('label'=>'Increase brand visibility', 'name'=>'brand_visibility'),
                                            array(  'label'=>'Showcase a new product or service', 'name'=>'showcase_product'),
                                            array(  'label'=>'Generate Leads', 'name'=>'generate_leads'),
                                            array(  'label'=>'Engage with a specific audience', 'name'=>'engage_audience'),
                                            array(  'label'=>'Other', 'name'=>'other_sponsor') 
										),
                                    ),
									array(
										'label' => 'Can you share more details?',
										'name' => 'sponsor_more',
										'type'=>'text',
										'required'=>'1',
										'place'=>'workcongress24'),
								),
								'partner' => array(
										array(
											'label' => 'What type of partnership are you interested in?',
											'name' => 'partnership',
											'type'=>'checkbox',
											'required'=>'1',
											'place'=>'workcongress24', 
											'option' => array(
                                                            array( 'label'=>'Content', 'name'=>'content'),
                                                            array(  'label'=>'Media and marketing', 'name'=>'media_marketing'),
                                                            array( 'label'=>'Technology Provider', 'name'=>'technology_provider'),
                                                            array( 'label'=>'community', 'name'=>'community'),
                                                            array('label'=>'Event Production and support', 'name'=>'event_production'),
                                                            array( 'label'=>'Other', 'name'=>'other_partner')
                                                        )
										),
										array(
											'label' => 'Can you describe your primary objectives for partnering with us? For example, are you looking to increase brand visibility, showcase a new product or service, generate leads, or engage with a specific audience segment?',
											'name' => 'partnership_more',
											'type'=>'text',
											'required'=>'1',
											'place'=>'workcongress24'
										),
								),

								'localhost' => array(
									array(
										'label' => 'Pick an option below: ',
										'name' => 'hostat',
										'type'=>'radio',
										'required'=>'1',
										'place'=>'workcongress24', 
										'option'=>array(
                                            array('label'=>'Content', 'name'=>'content'),
                                            array('label'=>'Media and marketing', 'name'=>'media_marketing'),
                                            array('label'=>'Technology Provider', 'name'=>'technology_provider'),
                                            array('label'=>'community', 'name'=>'community'),
                                            array('label'=>'Event Production and support', 'name'=>'event_production'),
                                            array('label'=>'Other', 'name'=>'other_localhost'),
                                        ),
									),
									array(
										'label' => 'Approximately how many people do you expect to attend?',
										'name' => 'people_attend',
										'type'=>'number',
										'required'=>'0',
										'place'=>'workcongress24',
									),
								),

								'speaker' => array(
									array(
										'label' => 'Please provide a title of your proposed talk or workshop.',
										'name' => 'speaker_title',
										'type'=>'text',
										'required'=>'1',
										'place'=>'workcongress24'),
									array('label' => 'Please provide a brief summary of your proposed talk or workshop.',
										'name' => 'speaker_abstract',
										'type'=>'text',
										'required'=>'1',
										'place'=>'workcongress24'),
									array(
										'label' => 'Please provide links to your previous talks.',
										'name' => 'speaker_links',
										'type'=>'text',
										'required'=>'1',
										'place'=>'workcongress24'),
								),

								'participant' => array(
									array(
										'label' => 'How did you hear about our conference?',
										'name' => 'participant_hear',
										'type'=>'radio',
										'required'=>'1',
										'place'=>'workcongress24', 
										'option'=>array(
                                                    array('label'=>'Social Media', 'name'=>'social_media_participant'),
													array(	'label'=>'Email Newsletter', 'name'=>'email_newsletter'),
													array(	'label'=>'Word of Mouth', 'name'=>'word_of_mouth'),
													array(	'label'=>'Other', 'name'=>'other_participant')
                                                        
                                                )
									),
									array(
										'label' => 'Tell me more',
										'name' => 'participant_hear_other',
										'type'=>'text',
										'required'=>'1',
										'place'=>'workcongress24',),
								),

								'volunteer' => array(
									array(
										'label' => 'What areas are you interested in volunteering for?',
										'name' => 'volunteer_interest',
										'type'=>'radio',
										'required'=>'1',
										'place'=>'workcongress24', 
										'option'=>array(
                                                    array('label'=>'Tech Support', 'name'=>'tech_support'),
													array(	'label'=>'Session Moderation', 'name'=>'session_moderation'),
													array(	'label'=>'Social Media', 'name'=>'social_media_volunteer'),
													array(	'label'=>'Marketing, Outreach', 'name'=>'marketing_outreach'),
													array(	'label'=>'Other', 'name'=>'other_volunteer')
                                        ),
									),
									array(
										'label' => 'Tell me more',
										'name' => 'volunteer_hear_other',
										'type'=>'text',
										'required'=>'1',
										'place'=>'workcongress24'),
									array(
										'label' => 'Describe your previous experience in this type of volunteering',
										'name' => 'volunteer_why',
										'type'=>'text',
										'required'=>'1',
										'place'=>'workcongress24'),
								),
								
							),
					),
					array(
						'label' => 'Additional Comments',
						'name' => 'comments',
						'required'=>'1',
						'type'=>'question',
						'place'=>'workcongress24'	,		
						'questions' => array( 
							array('label'=>'Any additional comments or questions?',
										'name' => 'additional_comments',
										'type'=>'textarea',
										'required'=>'0',
										'place'=>'workcongress24')
						)
					),  
					
					array(
							'label' =>'Submit',
							'name' => 'submit',
							'type'=>'button',
							'required'=>'0',
							'place'=>'common'
                        ),
			),
            "footer"=>array('footer_image','footer','img','0','common'),
    );   


$result = json_encode($form_field); 
//header('Content-Type: application/json; charset=utf-8');
return $result;
//taoh_exit(); 


?>