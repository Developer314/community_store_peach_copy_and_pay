<?php      

namespace Concrete\Package\CommunityStorePeachCopyAndPay;

use Route;
use \Concrete\Core\Package\Package;
use \Concrete\Package\CommunityStore\Src\CommunityStore\Payment\Method as PaymentMethod;


class Controller extends Package
{
    protected $pkgHandle = 'community_store_peach_copy_and_pay';
    protected $appVersionRequired = '8.0';
    protected $pkgVersion = '0.9.2';
    protected $packageDependencies = ['community_store'=>'2.0'];
    protected $pkgAutoloaderRegistries = array(
        'src/CommunityStore' => 'Concrete\Package\CommunityStorePeachCopyAndPay\Src\CommunityStore',
    );
    public function on_start()
    {
        Route::register('/checkout/creapaymentSession','\Concrete\Package\CommunityStorePeachCopyAndPay\Src\CommunityStore\Payment\Methods\CommunityStorePeachCopyAndPay\CommunityStorePeachCopyAndPayPaymentMethod::creapaymentSession');
        
        Route::register('/peach_payment_redirect','\Concrete\Package\CommunityStorePeachCopyAndPay\Src\CommunityStore\Payment\Methods\CommunityStorePeachCopyAndPay\CommunityStorePeachCopyAndPayPaymentMethod::getStatus');
        
    }


    public function getPackageDescription()
    {
        return t("Peach Copy and Pay Payment Method for Community Store");
    }

    public function getPackageName()
    {
        return t("Peach Copy And Pay Payment Method");
    }
    
    public function install()
    {
        $pkg = parent::install();
        $pm = new PaymentMethod();
        $pm->add('community_store_peach_copy_and_pay','Peach Payment Copy And Pay',$pkg);
    }
    public function uninstall()
    {
        $pm = PaymentMethod::getByHandle('community_store_peach_copy_and_pay');
        if ($pm) {
            $pm->delete();
        }
        $pkg = parent::uninstall();
    }

}
?>