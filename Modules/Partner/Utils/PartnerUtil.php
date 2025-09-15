<?php
namespace Modules\Partner\Utils;

use App\Business;
use Modules\Partner\Entities\Partner;
use Modules\Partner\Entities\PartnerReceipt;

class PartnerUtil extends \App\Utils\Util
{
    public function getListPartners($business_id) {
        $query = Partner::where('business_id', $business_id)->with(['category', 'locality', 'maritalStatus']);
        return $query;
    }

    public function getLedgerDetail($partner_id) {
        try {
            $partner = Partner::findOrFail($partner_id);

            
            return [
                'partner' => $partner,
            ];
        }catch(\Exception $e) {
            throw $e;
        }
    }
}