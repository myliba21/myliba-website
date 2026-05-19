'use strict';
const { createCoreService } = require('@strapi/strapi').factories;
module.exports = createCoreService('api::our-products.our-products');
