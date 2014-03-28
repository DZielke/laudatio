<?php
public $actsAs = array(
    'Elasticsearch.Searchable' => array(
        'debug_traces' => false,
        'searcher_enabled' => false,
        'searcher_action' => 'searcher',
        'searcher_param' => 'q',
        'searcher_serializer' => 'json_encode',
        'fake_fields' => array(
            '_label' => array('Product/description', 'BasketItem/description'),
        ),
        'index_name' => 'main',
        'index_chunksize' => 10000,
        'index_find_params' => array(
            'limit' => 1,
            'fields' => array(
                // It's important you name your fields.
                'subject',
                'from',
            ),
            'contain' => array(
                'Customer' => array(
                    // It's important you name your fields.
                    'fields' => array(
                        'id',
                        'name',
                    ),
                ),
                'TicketResponse' => array(
                    // It's important you name your fields.
                    'fields' => array(
                        'id',
                        'content',
                        'created',
                    ),
                ),
                'TicketObjectLink' => array(
                    // It's important you name your fields.
                    'fields' => array(
                        'foreign_model',
                        'foreign_id',
                    ),
                ),
                'TicketPriority' => array(
                    // It's important you name your fields.
                    'fields' => array(
                        'code',
                        'from',
                    ),
                ),
                'TicketQueue' => array(
                    // It's important you name your fields.
                    'fields' => array(
                        'name',
                    ),
                ),
            ),
            'order' => array(
                'Ticket.id' => 'DESC',
            ),
        ),
        'highlight' => array(
            'pre_tags' => array('<em class="highlight">'),
            'post_tags' => array('</em>'),
            'fields' => array(
                '_all' => array(
                    'fragment_size' => 200,
                    'number_of_fragments' => 1,
                ),
            ),
        ),
        'realtime_update' => false,
        'error_handler' => 'php',
        'static_url_generator' => array('{model}', 'url'),
        'enforce' => array(
            'Customer/id' => 123,
            // or a callback: '#Customer/id' => array('LiveUser', 'id'),
        ),
        'highlight_excludes' => array(
            // if you're always restricting results by customer, that
            // query should probably not be part of your highlight
            // instead of dumping _all and going over all fields except Customer/id,
            // you can also exclude it:
            'Customer/id',
        ),
    ),
);
?>