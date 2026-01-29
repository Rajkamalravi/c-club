<?php

$config_data_tag_category = [
    'hiring-talent' => ['Open to Work', 'Actively Hiring', 'Job Seeker', 'Can Refer', 'Can Scout', 'Job Hunt Help', 'Internships', 'Freelance Gigs'],
    'career-navigation' => ['Career Switch', 'Preparing for Interview', 'Need Mentor', 'Can Mentor', 'Upskilling'],
    'growth-exchange' => ['Skill Share', 'Ask Me Anything'],
    'collaboration-exchange' => ['Need Feedback', 'Creative Collab', 'Coding Collab', 'Bartering', 'Volunteering', 'Remote Work'],
    'startup-funding' => ['Need Cofounder', 'Need Funding', 'Seeking Capital', 'Active Investor', 'Investor Ready', 'Venture Scout', 'Seed Funding', 'Startup Mentor', 'Evaluating Pitches']
];

$config_data_tag_category_form = [
    'Open to Work' => [
        ['field_name' => 'preferred_cities',
            'field_type' => 'text',
            'field_value' => 'Preferred Cities'],
        ['field_name' => 'preferred_job_type',
            'field_type' => 'dropdown',
            'field_value' => 'Preferred Job Type',
            'dropdown_value' => ['Full-time', 'Part-time', 'Contract', 'Internship', 'Freelance/Project-based', 'Temporary', 'Open to Any']],
        ['field_name' => 'desired_job',
            'field_type' => 'text',
            'field_value' => 'Desired Job Titles'],
        ['field_name' => 'availability_timeline',
            'field_type' => 'dropdown',
            'field_value' => 'Availability Timeline',
            'dropdown_value' => ['Immediately', 'Within 1 month', '1-3 months', '3-6 months', '6+ months', 'Passively exploring']],
        ['field_name' => 'salary_expectations',
            'field_type' => 'number',
            'field_value' => 'Salary Expectations'],
        ['field_name' => 'career_level',
            'field_type' => 'dropdown',
            'field_value' => 'Career Level',
            'dropdown_value' => ['Entry-level', 'Mid-level', 'Senior', 'Executive', 'Director', 'C-suite', 'Founder']],
        ['field_name' => 'work_setup',
            'field_type' => 'dropdown',
            'field_value' => 'Work Setup',
            'dropdown_value' => ['Remote', 'Hybrid', 'On-site']],
    ],
    'Actively Hiring' => [
        ['field_name' => 'job_title',
            'field_type' => 'text',
            'field_value' => 'Job Titles Hiring For'],
        ['field_name' => 'hiring_level',
            'field_type' => 'dropdown',
            'field_value' => 'Hiring Level',
            'dropdown_value' => ['Internship', 'Entry-Level', 'Mid-Level', 'Senior-Level', 'Executive/C-Level', 'Multiple Levels', 'Freelance/Contract']],
        ['field_name' => 'hiring_urgency',
            'field_type' => 'dropdown',
            'field_value' => 'Hiring Urgency',
            'dropdown_value' => ['Immediate', 'Within 1 month', 'Next 3 months', 'Ongoing/Open-ended', 'Project-based needs']],
        ['field_name' => 'work_location',
            'field_type' => 'dropdown',
            'field_value' => 'Work Location',
            'dropdown_value' => ['Remote', 'Hybrid', 'On-site']],
        ['field_name' => 'employer_name',
            'field_type' => 'text',
            'field_value' => 'Employer/Company Name'],
        ['field_name' => 'industry',
            'field_type' => 'dropdown',
            'field_value' => 'Industry',
            'dropdown_value' => ['IT', 'Healthcare', 'Finance', 'Marketing', 'Manufacturing', 'Education', 'Retail', 'Legal', 'Consumer Goods', 'Other']],
    ],
    'Job Seeker' => [
        ['field_name' => 'js_job_title',
            'field_type' => 'text',
            'field_value' => 'Desired Job Titles'],
        ['field_name' => 'js_desired_job_level',
            'field_type' => 'dropdown',
            'field_value' => 'Desired Job Level',
            'dropdown_value' => ['Internship', 'Entry-Level', 'Associate/Junior', 'Mid-Level', 'Senior-Level', 'Executive/C-Level', 'Flexible/Any Level']],
        ['field_name' => 'js_job_search_status',
            'field_type' => 'dropdown',
            'field_value' => 'Job Search Status',
            'dropdown_value' => ['Actively Searching', 'Applying Casually', 'Exploring Opportunities', 'Employed but Looking', 'Recently Laid-off', 'Considering Offers Passively']],
        ['field_name' => 'js_preferred_cities',
            'field_type' => 'text',
            'field_value' => 'Preferred Cities'],
        ['field_name' => 'js_work_setup',
            'field_type' => 'dropdown',
            'field_value' => 'Work Setup',
            'dropdown_value' => ['Remote', 'Hybrid', 'On-site']],
    ],
    'Need Mentor' => [
        [
            'field_name' => 'mentorship_topics',
            'field_type' => 'dropdown',
            'field_value' => 'Mentorship Topics',
            'dropdown_value' => [
                'Career Guidance',
                'Leadership Development',
                'Technical Skills',
                'Soft Skills',
                'Startup Advice',
                'Job Search Support',
                'Networking & Industry Insights',
                'Personal Growth',
                'Other'
            ]
        ],
        [
            'field_name' => 'mentorship_duration',
            'field_type' => 'dropdown',
            'field_value' => 'Mentorship Duration',
            'dropdown_value' => [
                'Short-term (<3 months)',
                'Medium-term (3-6 months)',
                'Long-term (6+ months)',
                'Occasional/Ad hoc',
                'Flexible'
            ]
        ],
        [
            'field_name' => 'mentorship_format',
            'field_type' => 'dropdown',
            'field_value' => 'Preferred Mentorship Format',
            'dropdown_value' => [
                'Virtual',
                'In-person',
                'No Preference'
            ]
        ]
    ],
    'Can Mentor' => [
        ['field_name' => 'area_expertise',
            'field_type' => 'dropdown',
            'field_value' => 'Areas of Expertise',
            'dropdown_value' => ['Career Growth', 'Leadership', 'Technical Skills', 'Soft Skills', 'Startup Guidance', 'Job Search', 'Other']],
        ['field_name' => 'mentorship_style',
            'field_type' => 'dropdown',
            'field_value' => 'Mentorship Style',
            'dropdown_value' => ['Regular 1-on-1 Sessions', 'Occasional Check-ins', 'Group Mentorship', 'Ad hoc Advice', 'Formal Mentorship Programs', 'Online/Virtual Only']],
        ['field_name' => 'preferred_mentee_stage',
            'field_type' => 'dropdown',
            'field_value' => 'Preferred Mentee Stage',
            'dropdown_value' => ['Students/New Grads', 'Entry-Level Professionals', 'Mid-career Professionals', 'Senior Professionals', 'Entrepreneurs', 'Anyone Interested']],
        ['field_name' => 'availability',
            'field_type' => 'dropdown',
            'field_value' => 'Availability',
            'dropdown_value' => ['Few hours per week', 'Occasional', 'Monthly', 'Flexible']]
    ],
    'Bartering' => [
        ['field_name' => 'skills_offered',
            'field_type' => 'text',
            'field_value' => 'Skills/Services Offered'],
        ['field_name' => 'skills_needed',
            'field_type' => 'text',
            'field_value' => 'Skills/Services Needed'],
        ['field_name' => 'bartering_focus',
            'field_type' => 'dropdown',
            'field_value' => 'Bartering Focus',
            'dropdown_value' => ['Professional Services', 'Product Exchange', 'Skill Exchange', 'Consulting/Advice', 'Promotional Exchanges']],
        ['field_name' => 'negotiation_style',
            'field_type' => 'dropdown',
            'field_value' => 'Negotiation Style',
            'dropdown_value' => ['Structured Agreement', 'Open-ended Discussion', 'Quick Exchanges', 'Long-term Exchanges', 'Flexible Offers']]
    ],
    'Volunteering' => [
        ['field_name' => 'causes',
            'field_type' => 'dropdown',
            'field_value' => 'Causes/Industries Interested In',
            'dropdown_value' => ['Environment', 'Education', 'Healthcare', 'Social Work', 'Non-profits', 'Other']],
        ['field_name' => 'volunteering_availability',
            'field_type' => 'dropdown',
            'field_value' => 'Availability',
            'dropdown_value' => ['Weekly', 'Monthly', 'Flexible']],
        ['field_name' => 'commitment_level',
            'field_type' => 'dropdown',
            'field_value' => 'Commitment Level',
            'dropdown_value' => ['Single Event', 'Short-term Engagement', 'Regular/Ongoing', 'Flexible Availability', 'Occasional/Ad hoc']],
        ['field_name' => 'preferred_volunteer_area',
            'field_type' => 'dropdown',
            'field_value' => 'Preferred Volunteer Area',
            'dropdown_value' => ['Social Causes', 'Professional Skills', 'Community Events', 'Nonprofit Support', 'Disaster Relief', 'Environment/Climate Action']]
    ],
    'Remote Work' => [
        ['field_name' => 'job_roles',
            'field_type' => 'text',
            'field_value' => 'Job Roles Considered'],
        ['field_name' => 'time_zone_preferences',
            'field_type' => 'dropdown',
            'field_value' => 'Time Zone Preferences',
            'dropdown_value' => ['Any', 'UTC-8 to UTC-5', 'UTC-4 to UTC+1', 'UTC+2 to UTC+8', 'Other']],
        ['field_name' => 'remote_preference',
            'field_type' => 'dropdown',
            'field_value' => 'Remote Preference',
            'dropdown_value' => ['Fully Remote Only', 'Hybrid/Partially Remote', 'Remote Optional', 'Temporarily Remote', 'Flexible/Negotiable']],
        ['field_name' => 'remote_availability',
            'field_type' => 'dropdown',
            'field_value' => 'Remote Availability',
            'dropdown_value' => ['Immediately', 'Within 1 Month', '1-3 Months', 'Future Plans', 'Passively Interested']]
    ],
    'Can Refer' => [
        ['field_name' => 'referral_frequency',
            'field_type' => 'dropdown',
            'field_value' => 'Referral Frequency',
            'dropdown_value' => ['Very Frequently', 'Occasionally', 'Selectively', 'Rarely', 'On Request', 'Not Actively']],
        ['field_name' => 'referral_method',
            'field_type' => 'dropdown',
            'field_value' => 'Referral Method',
            'dropdown_value' => ['Direct Introduction', 'Internal Portal/Link', 'Formal Application', 'LinkedIn Referrals', 'Email/Message Introductions', 'Community Recommendations']],
        ['field_name' => 'companies_referrals',
            'field_type' => 'text',
            'field_value' => 'Companies/Industries Where Referrals Are Available']
    ],
    'Can Scout' => [
        ['field_name' => 'scouting_purpose',
            'field_type' => 'dropdown',
            'field_value' => 'Scouting Purpose',
            'dropdown_value' => ['Talent Acquisition', 'Industry Networking', 'Collaboration/Partnerships', 'Investment Opportunities', 'Startup Identification', 'Innovation Insights']],
        ['field_name' => 'interaction_preference',
            'field_type' => 'dropdown',
            'field_value' => 'Interaction Preference',
            'dropdown_value' => ['Direct Outreach', 'Observational', 'Introductions Only', 'Event Networking', 'Online Platforms', 'Passive Interest']]
    ],
    'Need Cofounder' => [
        ['field_name' => 'industry_domain',
            'field_type' => 'dropdown',
            'field_value' => 'Industry/Startup Domain',
            'dropdown_value' => ['Tech', 'Healthcare', 'Finance', 'Education', 'Consumer Goods', 'Other']],
        ['field_name' => 'skills_cofounder',
            'field_type' => 'text',
            'field_value' => 'Skills/Expertise Needed in a Cofounder'],
    ],
    'Career Switch' => [
        ['field_name' => 'current_industry',
            'field_type' => 'dropdown',
            'field_value' => 'Current Industry',
            'dropdown_value' => ['IT', 'Healthcare', 'Finance', 'Marketing', 'Manufacturing', 'Education', 'Retail', 'Legal', 'Other']],
        ['field_name' => 'target_industry',
            'field_type' => 'dropdown',
            'field_value' => 'Target Industry',
            'dropdown_value' => ['IT', 'Healthcare', 'Finance', 'Marketing', 'Manufacturing', 'Education', 'Retail', 'Legal', 'Other']],
        ['field_name' => 'transferable_skills',
            'field_type' => 'text',
            'field_value' => 'Transferable Skills'],
        ['field_name' => 'switch_stage',
            'field_type' => 'dropdown',
            'field_value' => 'Switch Stage',
            'dropdown_value' => ['Just Exploring', 'Actively Planning', 'Currently Transitioning', 'Recently Switched', 'Long-term Goal', 'Open to Opportunities']],
        ['field_name' => 'switch_timeline',
            'field_type' => 'dropdown',
            'field_value' => 'Switch Timeline',
            'dropdown_value' => ['Immediate', 'Within 3 months', 'Within 6 months', '6-12 months', 'Over 12 months', 'Flexible/No Rush']]
    ],
    'Upskilling' => [
        ['field_name' => 'skills_learning',
            'field_type' => 'text',
            'field_value' => 'Skills/Technologies Learning'],
        ['field_name' => 'preferred_learning_mode',
            'field_type' => 'dropdown',
            'field_value' => 'Preferred Learning Mode',
            'dropdown_value' => ['Courses', 'Mentorship', 'Projects', 'Bootcamp', 'Other']],
        ['field_name' => 'learning_goals',
            'field_type' => 'dropdown',
            'field_value' => 'Learning Goals',
            'dropdown_value' => ['Skill Development', 'Certification Achievement', 'Career Advancement', 'Personal Interest', 'Industry Knowledge', 'Job Transition']],
        ['field_name' => 'learning_method',
            'field_type' => 'dropdown',
            'field_value' => 'Learning Method',
            'dropdown_value' => ['Online Courses', 'In-Person Workshops', 'Webinars/Seminars', 'Peer/Group Learning', 'Self-paced Tutorials', 'Certifications/Exams']]
    ],
    'Job Hunt Help' => [
        ['field_name' => 'support_offered',
            'field_type' => 'dropdown',
            'field_value' => 'Support Offered',
            'dropdown_value' => ['Resume Review', 'Cover Letter Review', 'Interview Preparation', 'Job Search Strategy', 'Networking Support', 'Application Guidance', 'Industry Insight', 'Mock Interviews', 'Other']],
        ['field_name' => 'availability',
            'field_type' => 'dropdown',
            'field_value' => 'Availability',
            'dropdown_value' => ['Weekly Availability', 'Monthly Availability', 'Occasionally', 'By Appointment', 'Group Sessions Only']],
    ],
    'Can Vouch' => [
        ['field_name' => 'type_of_professionals',
            'field_type' => 'text',
            'field_value' => 'Types of Professionals/Skills You Can Vouch For']
    ],
    'Freelance Gigs' => [
        [
            'field_name' => 'services_offered',
            'field_type' => 'text',
            'field_value' => 'Services Offered'
        ],
        [
            'field_name' => 'preferred_project_length',
            'field_type' => 'dropdown',
            'field_value' => 'Preferred Project Length',
            'dropdown_value' => [
                'Short-term (<1 month)',
                'Medium-term (1-6 months)',
                'Long-term (>6 months)',
                'Recurring/Retainer-based',
                'One-time Task',
                'Flexible'
            ]
        ],
        [
            'field_name' => 'availability_timeline',
            'field_type' => 'dropdown',
            'field_value' => 'Availability Timeline',
            'dropdown_value' => [
                'Immediately',
                'Within 1 month',
                'Scheduled Availability',
                'Limited Availability',
                'Passively Interested',
                'Open to Discuss'
            ]
        ],
        [
            'field_name' => 'engagement_type',
            'field_type' => 'dropdown',
            'field_value' => 'Engagement Type',
            'dropdown_value' => [
                'Part-time',
                'Full-time',
                'Flexible'
            ]
        ]
    ],
    'Internships' => [
        ['field_name' => 'desired_industry',
            'field_type' => 'dropdown',
            'field_value' => 'Desired Industry or Role',
            'dropdown_value' => ['IT', 'Marketing', 'Finance', 'Healthcare', 'Other']],
        ['field_name' => 'internship_duration',
            'field_type' => 'dropdown',
            'field_value' => 'Internship Duration',
            'dropdown_value' => ['Short-term (1-3 months)', 'Medium-term (3-6 months)', 'Long-term (6+ months)', 'Flexible/Negotiable', 'Academic Semester', 'Summer Internship']],
        ['field_name' => 'compensation_preference',
            'field_type' => 'dropdown',
            'field_value' => 'Compensation Preference',
            'dropdown_value' => ['Paid Only', 'Unpaid/Credit Only', 'Open to Either', 'Stipend-Based', 'Performance-Based Compensation']],
    ],
    'Need Funding' => [
        ['field_name' => 'startup_stage',
            'field_type' => 'dropdown',
            'field_value' => 'Startup Stage',
            'dropdown_value' => ['Idea', 'Pre-seed', 'Seed', 'Series A', 'Growth Stage', 'Other']],
        ['field_name' => 'funding_amount_needed',
            'field_type' => 'text',
            'field_value' => 'Funding Amount Needed'],
    ],
    'Need Feedback' => [
        ['field_name' => 'feedback_needed_on',
            'field_type' => 'dropdown',
            'field_value' => 'Feedback Needed On',
            'dropdown_value' => ['Project/Product Idea', 'Resume/Profile', 'Portfolio/Work Samples', 'Startup Pitch', 'Career Direction', 'Presentation/Communication', 'Technical Work']],
        ['field_name' => 'feedback_urgency',
            'field_type' => 'dropdown',
            'field_value' => 'Feedback Urgency',
            'dropdown_value' => ['Immediate (Within Days)', 'Within 1 Week', 'Flexible Timeline', 'No Deadline', 'Ongoing Feedback']]
    ],
    'Creative Collab' => [
        [
            'field_name' => 'creative_focus',
            'field_type' => 'text',
            'field_value' => 'Creative Skills or Areas of Interest'
        ],
        [
            'field_name' => 'collaboration_duration',
            'field_type' => 'dropdown',
            'field_value' => 'Collaboration Duration',
            'dropdown_value' => [
                'Short-Term',
                'Long-Term',
                'Flexible/Exploratory'
            ]
        ],
        [
            'field_name' => 'collaboration_scope',
            'field_type' => 'dropdown',
            'field_value' => 'Project Scope',
            'dropdown_value' => [
                'Individual Project',
                'Group/Community Collaboration',
                'Event-Based Initiative',
                'Open-Ended Brainstorming'
            ]
        ],
        [
            'field_name' => 'work_style',
            'field_type' => 'dropdown',
            'field_value' => 'Preferred Collaboration Style',
            'dropdown_value' => [
                'Remote Only',
                'Regular Meetings',
                'Flexible Check-Ins',
                'Structured Milestones',
                'Open Creative Sessions'
            ]
        ]
    ],
    'Coding Collab' => [
        ['field_name' => 'tech_languages',
            'field_type' => 'text',
            'field_value' => 'Tech Stack/Languages'],
        ['field_name' => 'project_type',
            'field_type' => 'dropdown',
            'field_value' => 'Project Type',
            'dropdown_value' => ['Open Source Contribution', 'Hackathon Participation', 'Startup/Product Development', 'Side Project', 'Technical Challenge', 'Educational Project']],
        ['field_name' => 'collaboration_method',
            'field_type' => 'dropdown',
            'field_value' => 'Collaboration Method',
            'dropdown_value' => ['Pair Programming', 'Remote Collaboration', 'Code Reviews', 'Occasional Support', 'Continuous Integration', 'Asynchronous Coding']]
    ],
    'Ask Me Anything' => [
        [
            'field_name' => 'expertise_areas',
            'field_type' => 'text',
            'field_value' => 'Areas of Expertise'
        ],
        [
            'field_name' => 'ama_topic',
            'field_type' => 'dropdown',
            'field_value' => 'Topic of AMA',
            'dropdown_value' => [
                'Career Advice',
                'Industry Insights',
                'Personal Journey',
                'Company or Role-Specific',
                'Skill-Specific Advice',
                'Entrepreneurship',
                'Open-Ended Questions'
            ]
        ],
        [
            'field_name' => 'response_style',
            'field_type' => 'dropdown',
            'field_value' => 'Preferred Response Style',
            'dropdown_value' => [
                'Live Session',
                'Scheduled Replies',
                'Written Answers',
                'Video Replies',
                'Casual Chat',
                'Group AMA'
            ]
        ]
    ],
    'Preparing for Interview' => [
        [
            'field_name' => 'job_industry',
            'field_type' => 'text',
            'field_value' => 'Target Role/Industry'
        ],
        [
            'field_name' => 'area_preparation',
            'field_type' => 'dropdown',
            'field_value' => 'Preparation Focus Areas',
            'dropdown_value' => [
                'Technical',
                'Behavioral',
                'Case Study',
                'Coding Interview',
                'System Design',
                'Other'
            ]
        ],
        [
            'field_name' => 'interview_stage',
            'field_type' => 'dropdown',
            'field_value' => 'Interview Stage',
            'dropdown_value' => [
                'Pre-interview Prep',
                'Initial Screening',
                'Technical Round',
                'Panel/Group Round',
                'Final Round',
                'Offer Negotiation'
            ]
        ],
        [
            'field_name' => 'support_type',
            'field_type' => 'dropdown',
            'field_value' => 'Support Type Preferred',
            'dropdown_value' => [
                'Mock Interviews',
                'Company-specific Advice',
                'General Tips',
                'Technical Preparation Help',
                'Negotiation Guidance',
                'Confidence Coaching / Soft Skills'
            ]
        ]
    ],
    'Skill Share' => [
        ['field_name' => 'skills_teach',
            'field_type' => 'text',
            'field_value' => 'Skills You Can Teach'],
        ['field_name' => 'frequency_of_sharing',
            'field_type' => 'dropdown',
            'field_value' => 'Frequency of Sharing',
            'dropdown_value' => ['Weekly', 'Monthly', 'Quarterly', 'Occasionally', 'Upon Request', 'Event-based Only']],
        ['field_name' => 'preferred_sharing_format',
            'field_type' => 'dropdown',
            'field_value' => 'Preferred Sharing Format',
            'dropdown_value' => ['Workshops', 'Webinars', 'Blog Posts', 'Videos/Tutorials', 'Group Discussions', '1-on-1 Chats', 'Community Q&A']]
    ],
    'Seeking Capital' => [
        ['field_name' => 'investment_range_needed',
            'field_type' => 'text',
            'field_value' => 'Investment Range Needed'],
        ['field_name' => 'business_sector',
            'field_type' => 'dropdown',
            'field_value' => 'Business Sector',
            'dropdown_value' => ['IT', 'Healthcare', 'Finance', 'Education', 'Consumer Goods', 'Other']],
    ],
    'Active Investor' => [
        ['field_name' => 'investment_sectors',
            'field_type' => 'dropdown',
            'field_value' => 'Investment Sectors',
            'dropdown_value' => ['IT', 'Healthcare', 'Finance', 'Education', 'Consumer Goods', 'Other']],
        ['field_name' => 'ticket_size_range',
            'field_type' => 'dropdown',
            'field_value' => 'Ticket Size Range',
            'dropdown_value' => ['<$50K', '$50K-$250K', '$250K-$1M', '$1M+', 'Other']],
    ],
    'Investor Ready' => [
        ['field_name' => 'business_stage',
            'field_type' => 'dropdown',
            'field_value' => 'Business Stage',
            'dropdown_value' => ['Early', 'Growth', 'Scaling', 'Other']],
        ['field_name' => 'funding_goal',
            'field_type' => 'text',
            'field_value' => 'Funding Goal'],
    ],
    'Venture Scout' => [
        ['field_name' => 'industries_scouting_for',
            'field_type' => 'dropdown',
            'field_value' => 'Industries Scouting For',
            'dropdown_value' => ['IT', 'Healthcare', 'Finance', 'Education', 'Consumer Goods', 'Other']],
        ['field_name' => 'funding_stages_covered',
            'field_type' => 'dropdown',
            'field_value' => 'Funding Stages Covered',
            'dropdown_value' => ['Pre-seed', 'Seed', 'Series A', 'Later Stage', 'Other']],
    ],
    'Seed Funding' => [
        ['field_name' => 'investment_amount_sought',
            'field_type' => 'number',
            'field_value' => 'Investment Amount Sought'],
        ['field_name' => 'startup_industry',
            'field_type' => 'dropdown',
            'field_value' => 'Startup Industry',
            'dropdown_value' => ['IT', 'Healthcare', 'Finance', 'Education', 'Consumer Goods', 'Other']]
    ],
    'Startup Mentor' => [
        ['field_name' => 'areas_mentorship',
            'field_type' => 'dropdown',
            'field_value' => 'Areas of Mentorship',
            'dropdown_value' => ['Fundraising', 'Growth', 'Product Development', 'Team Building', 'Other']]
    ],
    'Evaluating Pitches' => [
        ['field_name' => 'preferred_industries',
            'field_type' => 'dropdown',
            'field_value' => 'Preferred Industries',
            'dropdown_value' => ['IT', 'Healthcare', 'Finance', 'Education', 'Consumer Goods', 'Other']],
        ['field_name' => 'startups_considered',
            'field_type' => 'dropdown',
            'field_value' => 'Stage of Startups Considered',
            'dropdown_value' => ['Idea', 'Pre-seed', 'Seed', 'Series A', 'Growth Stage', 'Other']]
    ]
];

$config_data_directory_flags_to_show = [
    'open-to-work' => ['Actively Hiring', 'Can Refer'],
    'actively-hiring' => ['Open to Work', 'Job Seeker'],
    'job-seeker' => ['Actively Hiring', 'Can Refer'],
    'can-refer' => ['Open to Work', 'Actively Hiring', 'Job Seeker'],
    'can-scout' => ['Open to Work', 'Actively Hiring', 'Job Seeker'],
    'job-hunt-help' => ['Open to Work', 'Actively Hiring', 'Job Seeker', 'Can Refer'],
    'internships' => ['Actively Hiring', 'Can Refer', 'Internships'],
    'freelance-gigs' => ['Actively Hiring', 'Can Refer', 'Freelance Gigs'],
    'career-switch' => ['Open to Work', 'Job Seeker', 'Can Refer', 'Career Switch'],
    'preparing-for-interview' => ['Open to Work', 'Job Seeker', 'Preparing for Interview'],
    'need-mentor' => ['Can Mentor'],
    'can-mentor' => ['Need Mentor'],
    'upskilling' => ['Upskilling'],
    'skill-share' => ['Skill Share'],
    'ask-me-anything' => ['Upskilling', 'Need Feedback'],
    'need-feedback' => ['Need Feedback'],
    'creative-collab' => ['Creative Collab'],
    'coding-collab' => ['Coding Collab'],
    'bartering' => ['Open to Work', 'Bartering'],
    'volunteering' => ['Actively Hiring', 'Volunteering'],
    'remote-work' => ['Actively Hiring'],
    'need-cofounder' => ['Open to Work', 'Need Cofounder', 'Startup Mentor'],
    'need-funding' => ['Need Funding', 'Active Investor', 'Startup Mentor'],
    'seeking-capital' => ['Seeking Capital', 'Active Investor'],
    'active-investor' => ['Need Cofounder', 'Need Funding', 'Seeking Capital', 'Seed Funding'],
    'investor-ready' => ['Active Investor'],
    'venture-scout' => ['Need Cofounder', 'Need Funding', 'Seeking Capital', 'Seed Funding', 'Evaluating Pitches'],
    'seed-funding' => ['Active Investor'],
    'startup-mentor' => ['Need Cofounder', 'Need Funding', 'Seeking Capital', 'Seed Funding', 'Evaluating Pitches'],
    'evaluating-pitches' => ['Need Cofounder', 'Need Funding', 'Seeking Capital', 'Seed Funding', 'Evaluating Pitches']
];

$config_data_industry_categories = [
    "agri" => "Agriculture & Forestry",
    "arts" => "Arts & Entertainment",
    "auto" => "Automotive",
    "aero" => "Aviation & Aerospace",
    "bank" => "Banking & Finance",
    "bio" => "Biotechnology",
    "che" => "Chemicals",
    "cons" => "Construction",
    "good" => "Consumer Goods & Services",
    "space" => "Defense & Space",
    "edu" => "Education",
    "ene" => "Energy & Utilities",
    "engi" => "Engineering",
    "envi" => "Environmental Services",
    "fash" => "Fashion & Apparel",
    "food" => "Food & Beverages",
    "govt" => "Government & Public Sector",
    "heal" => "Healthcare & Pharmaceuticals",
    "tour" => "Hospitality & Tourism",
    "tech" => "Information Technology",
    "ins" => "Insurance",
    "legal" => "Legal Services",
    "manu" => "Manufacturing",
    "mari" => "Maritime",
    "mark" => "Marketing & Advertising",
    "media" => "Media & Communications",
    "mini" => "Mining & Metals",
    "non" => "Non-Profit & NGO",
    "prof" => "Professional Services",
    "real" => "Real Estate",
    "ret" => "Retail",
    "sports" => "Sports & Recreation",
    "tele" => "Telecommunications",
    "logi" => "Transport & Logistics",
    "other" => "Others (for industries not listed above)"
];

$config_data_role_types = [
    "remo" => "Remote Work",
    "full" => "Full Time",
    "part" => "Part Time",
    "temp" => "Temporary",
    "free" => "Freelance",
    "cont" => "Contract",
    "pdin" => "Paid Internship",
    "unin" => "Unpaid Internship",
    "voln" => "Volunteer"
];

$config_data_ntw_default_channels = [
    [
        'name' => 'Introduction',
        'description' => 'Welcome everyone! Introduce yourself, share what brings you here, and connect with others.'
    ],
    [
        'name' => 'Hiring and Talent',
        'description' => 'A space for hiring announcements, job seekers, and internship opportunities. Connect employers with talent.'
    ],
    [
        'name' => 'Career Navigation',
        'description' => 'Discuss mentoring, upskilling, and career transitions. Share guidance, resources, and success stories.'
    ],
    [
        'name' => 'Collaboration and Exchange',
        'description' => 'Find partners for volunteering, creative or coding collaborations, and get peer feedback on projects.'
    ],
    [
        'name' => 'Startup and Funding',
        'description' => 'Explore startup ideas, co-founder searches, investment discussions, mentorship, and product feedback.'
    ]
];

$config_data_ntw_welcome_messages = [
    "👋 Welcome! Want to share your name, what you do, and one fun fact about yourself?",
    "🌟 Tell us a little about what brought you here today.",
    "🤝 What’s one thing you’d love others here to know about you?",
    "💡 Share your current role or interest area—and what excites you most about it.",
    "🎯 What’s one goal you’re focusing on this month (career, learning, or personal)?",
    "✨ If your journey was a headline today, what would it read?",
    "🌍 What kind of people or conversations are you hoping to connect with here?",
    "🔥 If you could sum yourself up in 3 emojis, what would they be?",
    "📢 Share one skill, project, or interest you’d love to talk about with others.",
    "🚀  Drop a “Hello” and add one thing you’re curious about right now.",
    "👋 Welcome! Want to share your name, what you do, and one fun fact?",
    "🌟 Tell us who you are and what brought you here today.",
    "🤝 Introductions time: where are you based and what’s your current focus?",
    "💡 Share a quick intro: your role, your interests, or a project you’re excited about.",
    "🎯 If you had to sum yourself up in a short line, what would it be?",
    "✨ Let’s connect: what’s one thing you’d love others here to know about you?",
    "🌍 Where are you joining from, and what kind of conversations are you looking for?",
    "🔑 Share three things: your name, your passion, and something unique about you.",
    "🔥 Icebreaker: what’s one skill or hobby that defines you outside of work?",
    "🚀 Say hi and tell us one goal you’re working toward right now.",
];

define('DOJO_EVENT_LIST_MESSAGE',[
    [
        "name" => "Please login to Register for the upcoming event which is going to live now", //{EVENT}
        "expectations" => [
            "user_logged_in" => false
        ]
    ],
    [
        "name" => "Complete the profile to Join the event and explore more",
        "expectations" => [
            "user_logged_in" => true,
            "profile_complete" => 0
        ]
    ],
    [
        "name" => "Register for the upcoming event which is going to live",
        "expectations" => [
            "user_logged_in" => true,
            "is_rsvp" => 0
        ]
    ],
    [
        "name" => "Your event is going to live next, join the event ",
        "expectations" => [
            "user_logged_in" => true,
            "is_rsvp" => 1
        ]
    ],
]);


define('DOJO_EVENT_DETAIL_MESSAGE',[
    [
        "name" => "Please login to Register for this event and explore more",
        "expectations" => [
            "user_logged_in" => false
        ]
    ],
    [
        "name" => "This event is live now, Please login and register to join the event",
        "expectations" => [
            "user_logged_in" => false,
            "is_rsvp" => 0,
            "is_event_live" => 1
        ]
    ],
    [
        "name" => "This event contains sponsor, Login and register to become a sponsor before event is live",
        "expectations" => [
            "user_logged_in" => false,
            "is_rsvp" => 0,
            "is_event_live" => 0,
            "is_sponsor_enabled"=>1
        ]
    ],
    [
        "name" => "This Event contains exhibitor, Login and register to explore more about exhibitor",
        "expectations" => [
            "user_logged_in" => false,
            "is_rsvp" => 0,
            "is_exhibitor_enabled"=>1,
        ]
    ],
    [
        "name" => "This event contains speaker, Login and register to explore more about speaker",
        "expectations" => [
            "user_logged_in" => false,
            "is_rsvp" => 0,
            "is_speaker_enabled"=>1,
        ]
    ],
    [
        "name" => "Complete the profile to Join the event and explore more",
        "expectations" => [
            "user_logged_in" => true,
            "is_rsvp" => 1,
            "profile_complete" => 0
        ]
    ],
    [
        "name" => "This event is live now, Register to join the event",
        "expectations" => [
            "user_logged_in" => true,
            "is_rsvp" => 0,
            "is_event_live" => 1
        ]
    ],
    [
        "name" => "This event contains sponsor, Register to become a sponsor before event is live",
        "expectations" => [
            "user_logged_in" => true,
            "is_rsvp" => 0,
            "is_event_live" => 0,
            "is_sponsor_enabled"=>1
        ]
    ],
    [
        "name" => "This event contains exhibitor, Register to the event and explore more about exhibitor",
        "expectations" => [
            "user_logged_in" => true,
            "is_rsvp" => 0,
            "is_event_live" => 0,
            "is_exhibitor_enabled"=>1,
        ]
    ],
    [
        "name" => "This event contains speaker, Register to the event and explore more about speaker",
        "expectations" => [
            "user_logged_in" => true,
            "is_rsvp" => 0,
            "is_speaker_enabled"=>1,
        ]
    ],
]);