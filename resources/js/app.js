import './bootstrap';

import { initAdminProductGallery } from './admin/product-gallery';
import { initAdminShipmentSettings } from './admin/shipment-settings';
import { initAdminProductVariants } from './admin/product-variants';
import { initAddressBook } from './shared/address-book';
import { initTrix } from './shared/trix';
import { initCheckoutSelector } from './storefront/checkout-selector';
import { initStorefrontCart } from './storefront/cart';
import { initCustomerMenu } from './storefront/customer-menu';
import { initPasswordToggle } from './storefront/password-toggle';
import { initStorefrontAddressManager } from './storefront/address-manager';
import { initStorefrontProfile } from './storefront/profile';
import { initStorefrontProductGallery } from './storefront/product-gallery';

initTrix();
initAdminProductGallery();
initAdminShipmentSettings();
initAdminProductVariants();
initStorefrontProductGallery();
initCustomerMenu();
initPasswordToggle();
initStorefrontAddressManager();
initStorefrontProfile();
initAddressBook();
initCheckoutSelector();
initStorefrontCart();
