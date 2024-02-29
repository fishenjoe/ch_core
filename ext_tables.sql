#
# Adjustments for table 'pages'
#
CREATE TABLE pages
(
    ch_scroll_to_content                TINYINT(4) UNSIGNED DEFAULT '0' NOT NULL,
    ch_quickrequest_disabled            TINYINT(4) UNSIGNED DEFAULT '0' NOT NULL,
    ch_hide_in_mobile_menu              TINYINT(4) UNSIGNED DEFAULT '0' NOT NULL,
    ch_heritable_content_disabled       TINYINT(4) UNSIGNED DEFAULT '0' NOT NULL,
    ch_include_in_sitemap_menu          TINYINT(4) UNSIGNED DEFAULT '0' NOT NULL,
    ch_include_in_sitemap_menu_children TINYINT(4) UNSIGNED DEFAULT '0' NOT NULL,
    ch_custom_field                     VARCHAR(128)        DEFAULT ''  NOT NULL,
    ch_schema_org_image                 INT(11) UNSIGNED    DEFAULT '0' NOT NULL,
    ch_schema_org_rating_cur_value      VARCHAR(32)         DEFAULT ''  NOT NULL,
    ch_schema_org_rating_max_value      VARCHAR(32)         DEFAULT ''  NOT NULL,
    ch_schema_org_rating_amount         VARCHAR(32)         DEFAULT ''  NOT NULL
);

#
# Adjustments for table 'sys_file_reference'
#
CREATE TABLE sys_file_reference
(
    ch_video_attributes         VARCHAR(255)     DEFAULT ''  NOT NULL,
    ch_video_fallback           INT(11) UNSIGNED DEFAULT '0' NOT NULL,
    ch_video_sources            INT(11) UNSIGNED DEFAULT '0' NOT NULL,
    ch_video_sources_resolution VARCHAR(64)      DEFAULT ''  NOT NULL
);
