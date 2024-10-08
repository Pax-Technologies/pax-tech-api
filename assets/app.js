import 'datatables.net-bs5/css/dataTables.bootstrap5.min.css';
import './styles/PT-theme.scss';
import 'bootstrap/dist/css/bootstrap.min.css';
import './styles/app.css';
import '@symfony/ux-dropzone/dist/style.min.css'


/*
 * Welcome to your app's main JavaScript file!
 *
 * This file will be included onto the page via the importmap() Twig function,
 * which should already be in your base.html.twig.
 */

import $ from 'jquery';

global.$ = global.jQuery = $;
import 'bootstrap';
import './bootstrap';
import 'datatables.net';

import 'datatables.net-bs5';

$(document).ready( function () {
    $('#invoices-table').DataTable({
        "order": [[ 1, "asc" ]] // Trie la 2ème colonne en ordre croissant
    });
} );
