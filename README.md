# Pendaftaran

[![Join the chat at https://gitter.im/pendaftaran/Lobby](https://badges.gitter.im/pendaftaran/Lobby.svg)](https://gitter.im/pendaftaran/Lobby?utm_source=badge&utm_medium=badge&utm_campaign=pr-badge&utm_content=badge)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/bantenprov/pendaftaran/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/bantenprov/pendaftaran/?branch=master)
[![Build Status](https://scrutinizer-ci.com/g/bantenprov/pendaftaran/badges/build.png?b=master)](https://scrutinizer-ci.com/g/bantenprov/pendaftaran/build-status/master)
[![Latest Stable Version](https://poser.pugx.org/bantenprov/pendaftaran/v/stable)](https://packagist.org/packages/bantenprov/pendaftaran)
[![Total Downloads](https://poser.pugx.org/bantenprov/pendaftaran/downloads)](https://packagist.org/packages/bantenprov/pendaftaran)
[![Latest Unstable Version](https://poser.pugx.org/bantenprov/pendaftaran/v/unstable)](https://packagist.org/packages/bantenprov/pendaftaran)
[![License](https://poser.pugx.org/bantenprov/pendaftaran/license)](https://packagist.org/packages/bantenprov/pendaftaran)
[![Monthly Downloads](https://poser.pugx.org/bantenprov/pendaftaran/d/monthly)](https://packagist.org/packages/bantenprov/pendaftaran)
[![Daily Downloads](https://poser.pugx.org/bantenprov/pendaftaran/d/daily)](https://packagist.org/packages/bantenprov/pendaftaran)


Pendaftaran

### modul ini membutuhkan :
`vue-trust`

### Install via composer

- Development snapshot

```bash
$ composer require bantenprov/pendaftaran:dev-master
```

- Latest release:

```bash
$ composer require bantenprov/pendaftaran
```

### Install `vue-trust`

```bash
$ composer require bantenprov/vue-trust:dev-master
```

Vue Trust Documentation : <a href="https://github.com/bantenprov/vue-trust">Vue trust</a>


### Download via github

```bash
$ git clone https://github.com/bantenprov/pendaftaran.git
```

#### Edit `config/app.php` :

```php
'providers' => [

    /*
    * Laravel Framework Service Providers...
    */
    Illuminate\Auth\AuthServiceProvider::class,
    Illuminate\Broadcasting\BroadcastServiceProvider::class,
    Illuminate\Bus\BusServiceProvider::class,
    Illuminate\Cache\CacheServiceProvider::class,
    Illuminate\Foundation\Providers\ConsoleSupportServiceProvider::class,
    Illuminate\Cookie\CookieServiceProvider::class,
    //....
    Bantenprov\Pendaftaran\PendaftaranServiceProvider::class,
```

#### Lakukan migrate :

```bash
$ php artisan migrate
```

#### Publish database seeder :

```bash
$ php artisan vendor:publish --tag=pendaftaran-seeds
```

#### Lakukan auto dump :

```bash
$ composer dump-autoload
```

#### Lakukan seeding :

```bash
$ php artisan db:seed --class=BantenprovPendaftaranSeeder
```

#### Lakukan publish component vue :

```bash
$ php artisan vendor:publish --tag=pendaftaran-assets
$ php artisan vendor:publish --tag=pendaftaran-public
```
#### Tambahkan route di dalam file : `resources/assets/js/routes.js` :

```javascript
{
    path: '/dashboard',
    redirect: '/dashboard/home',
    component: layout('Default'),
    children: [
        //== ...
        {
         path: '/dashboard/pendaftaran',
         components: {
            main: resolve => require(['./components/views/bantenprov/pendaftaran/DashboardPendaftaran.vue'], resolve),
            navbar: resolve => require(['./components/Navbar.vue'], resolve),
            sidebar: resolve => require(['./components/Sidebar.vue'], resolve)
          },
          meta: {
            title: "Pendaftaran"
           }
       },
        //== ...
    ]
},
```

```javascript
{
    path: '/admin',
    redirect: '/admin/dashboard/home',
    component: layout('Default'),
    children: [
        //== ...
        {
            path: '/admin/pendaftaran',
            components: {
                main: resolve => require(['./components/bantenprov/pendaftaran/Pendaftaran.index.vue'], resolve),
                navbar: resolve => require(['./components/Navbar.vue'], resolve),
                sidebar: resolve => require(['./components/Sidebar.vue'], resolve)
            },
            meta: {
                title: "Pendaftaran"
            }
        },
        {
            path: '/admin/pendaftaran/create',
            components: {
                main: resolve => require(['./components/bantenprov/pendaftaran/Pendaftaran.add.vue'], resolve),
                navbar: resolve => require(['./components/Navbar.vue'], resolve),
                sidebar: resolve => require(['./components/Sidebar.vue'], resolve)
            },
            meta: {
                title: "Add Pendaftaran"
            }
        },
        {
            path: '/admin/pendaftaran/:id',
            components: {
                main: resolve => require(['./components/bantenprov/pendaftaran/Pendaftaran.show.vue'], resolve),
                navbar: resolve => require(['./components/Navbar.vue'], resolve),
                sidebar: resolve => require(['./components/Sidebar.vue'], resolve)
            },
            meta: {
                title: "View Pendaftaran"
            }
        },
        {
            path: '/admin/pendaftaran/:id/edit',
            components: {
                main: resolve => require(['./components/bantenprov/pendaftaran/Pendaftaran.edit.vue'], resolve),
                navbar: resolve => require(['./components/Navbar.vue'], resolve),
                sidebar: resolve => require(['./components/Sidebar.vue'], resolve)
            },
            meta: {
                title: "Edit Pendaftaran"
            }
        },
        //== ...
    ]
},
```
#### Edit menu `resources/assets/js/menu.js`

```javascript
{
    name: 'Dashboard',
    icon: 'fa fa-dashboard',
    childType: 'collapse',
    childItem: [
        //== ...
        {
        name: 'Pendaftaran',
        link: '/dashboard/pendaftaran',
        icon: 'fa fa-angle-double-right'
        },
        //== ...
    ]
},
```

```javascript
{
    name: 'Admin',
    icon: 'fa fa-lock',
    childType: 'collapse',
    childItem: [
        //== ...
        {
        name: 'Pendaftaran',
        link: '/admin/pendaftaran',
        icon: 'fa fa-angle-double-right'
        },
        //== ...
    ]
},
```

#### Tambahkan components `resources/assets/js/components.js` :

```javascript
//== Pendaftaran

import Pendaftaran from './components/bantenprov/pendaftaran/Pendaftaran.chart.vue';
Vue.component('echarts-pendaftaran', Pendaftaran);

import PendaftaranKota from './components/bantenprov/pendaftaran/PendaftaranKota.chart.vue';
Vue.component('echarts-pendaftaran-kota', PendaftaranKota);

import PendaftaranTahun from './components/bantenprov/pendaftaran/PendaftaranTahun.chart.vue';
Vue.component('echarts-pendaftaran-tahun', PendaftaranTahun);

import PendaftaranAdminShow from './components/bantenprov/pendaftaran/PendaftaranAdmin.show.vue';
Vue.component('admin-view-pendaftaran-tahun', PendaftaranAdminShow);

//== Echarts Group Egoverment

import PendaftaranBar01 from './components/views/bantenprov/pendaftaran/PendaftaranBar01.vue';
Vue.component('pendaftaran-bar-01', PendaftaranBar01);

import PendaftaranBar02 from './components/views/bantenprov/pendaftaran/PendaftaranBar02.vue';
Vue.component('pendaftaran-bar-02', PendaftaranBar02);

//== mini bar charts
import PendaftaranBar03 from './components/views/bantenprov/pendaftaran/PendaftaranBar03.vue';
Vue.component('pendaftaran-bar-03', PendaftaranBar03);

import PendaftaranPie01 from './components/views/bantenprov/pendaftaran/PendaftaranPie01.vue';
Vue.component('pendaftaran-pie-01', PendaftaranPie01);

import PendaftaranPie02 from './components/views/bantenprov/pendaftaran/PendaftaranPie02.vue';
Vue.component('pendaftaran-pie-02', PendaftaranPie02);

//== mini pie charts


import PendaftaranPie03 from './components/views/bantenprov/pendaftaran/PendaftaranPie03.vue';
Vue.component('pendaftaran-pie-03', PendaftaranPie03);

```

