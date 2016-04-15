/**
 * @author Emosys team
 * @copyright Copyright (c) 2016 Emosys Ltd (http://www.emosys.com)
 * @package Emosys_Banner
 */
var config = {
    paths:{
        'swiper': 'Emosys_Banner/js/swiper.min',
        'camera': 'Emosys_Banner/js/camera.min'
    },
    shim:{
        'swiper':{
            'deps':['jquery']
        },
        'camera':{
            'deps':['jquery']
        }
    }
};