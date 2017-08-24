<?php

namespace Pinecone;

/**
*   proof of concept for qurying multiple blogs in a multisite install
*/
class MS_Query
{
    protected $ms_sql = [];
    protected $wp_sql = [];

    public function __construct()
    {
        if (is_admin()) {
            return;
        }
            
        add_filter( 'posts_clauses', [$this, 'posts_clauses'], 10, 2 );
        add_filter( 'posts_request', [$this, 'posts_request'], 10, 2 );
    }

    /**
    *
    *   @param array
    *   @param WP_Query
    *   @return array
    */
    public function posts_clauses($pieces, $wp_query)
    {
        global $wpdb;

        $pieces['fields'] .= $wpdb->prepare( ", %d as `blog_id`", get_current_blog_id() );
        
        if ($wp_query->is_main_query()) {
            $this->wp_sql = $pieces;
            $this->wp_sql['db_table'] = $wpdb->posts;

            $args = array_merge( $wp_query->query_vars, ['multisite_query' => 1] );

            switch_to_blog( 1 );
            $q = new \WP_Query( $args );
            restore_current_blog();
        } elseif ($wp_query->get('multisite_query')) {
            $this->ms_sql = $pieces;
            $this->ms_sql['db_table'] = $wpdb->posts;
        }

        return $pieces;
    }

    /**
    *
    *   @param string
    *   @param WP_Query
    *   @return string
    */
    public function posts_request($sql, $wp_query)
    {
        if ($wp_query->is_main_query() && !empty($this->wp_sql) && !empty($this->ms_sql)) {
            $orderby = str_replace( $this->wp_sql['db_table'].'.', 'MSQUERY.', $this->wp_sql['orderby'] );

            if (empty($this->ms_sql['groupby'])) {
                $this->ms_sql['groupby'] = 1;
            }

            if (empty($this->wp_sql['groupby'])) {
                $this->wp_sql['groupby'] = 1;
            }
            
            $sql = "SELECT SQL_CALC_FOUND_ROWS MSQUERY.*
                    FROM (
                        SELECT {$this->wp_sql['fields']} FROM {$this->wp_sql['db_table']} {$this->wp_sql['join']} 
                        WHERE 1=1 {$this->wp_sql['where']} GROUP BY {$this->wp_sql['groupby']} 
                        
                        UNION
                        
                        SELECT {$this->ms_sql['fields']} FROM {$this->ms_sql['db_table']} {$this->ms_sql['join']}  
                        WHERE 1=1 {$this->ms_sql['where']} GROUP BY {$this->ms_sql['groupby']} 
                    ) AS MSQUERY

                    ORDER BY $orderby
                    {$this->wp_sql['limits']}";
        }

        return $sql;
    }
}
