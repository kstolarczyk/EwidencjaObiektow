/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */

// any CSS you import will output into a single css file (app.css in this case)
import '../css/app.scss';
// Need jQuery? Install it with "yarn add jquery", then uncomment to import it.
import $ from 'jquery';
import 'jquery-ui-dist/jquery-ui';
import 'bootstrap';
import bootbox from 'bootbox';
import './sb-admin';
import {alert, defaultModules, error, info, notice, success} from '@pnotify/core';
import * as PNotifyBootstrap4 from '@pnotify/bootstrap4';
import * as PNotifyFontAwesome5Fix from '@pnotify/font-awesome5-fix';
import * as PNotifyFontAwesome5 from '@pnotify/font-awesome5';

require('jszip');
require('pdfmake');
require('datatables.net-bs4')(window, $);
require('datatables.net-buttons-bs4')(window, $);
require('datatables.net-buttons/js/buttons.colVis')(window, $);
require('datatables.net-buttons/js/buttons.html5')(window, $);
require('datatables.net-responsive-bs4')(window, $);

global.$ = global.jQuery = $;
global.bootbox = bootbox;
defaultModules.set(PNotifyBootstrap4, {});
defaultModules.set(PNotifyFontAwesome5Fix, {});
defaultModules.set(PNotifyFontAwesome5, {});
global.alert = alert;
global.notice = notice;
global.info = info;
global.success = success;
global.error = error;

