<?php

declare(strict_types=1);

namespace Filway\Utils\sms;

use AlibabaCloud\SDK\Dysmsapi\V20170525\Dysmsapi;

use AlibabaCloud\SDK\Dysmsapi\V20170525\Models\SendSmsRequest;
use AlibabaCloud\SDK\Dysmsapi\V20170525\Models\SendSmsResponse;
use AlibabaCloud\Tea\Utils\Utils\RuntimeOptions;
use Darabonba\OpenApi\Models\Config;
use Filway\Component\Singleton;

class AliSms
{
    use Singleton;
    private $client;

    public function __construct(string $accessKeyId, string $accessKeySecret)
    {
        $this->client = self::createClient($accessKeyId, $accessKeySecret);
    }

    /**
     * 使用AK&SK初始化账号Client
     * @param string $accessKeyId
     * @param string $accessKeySecret
     * @return Dysmsapi Client
     */
    public function createClient($accessKeyId, $accessKeySecret)
    {
        $config = new Config(
            [
                // 您的AccessKey ID
                "accessKeyId" => $accessKeyId,
                // 您的AccessKey Secret
                "accessKeySecret" => $accessKeySecret
            ]
        );
        // 访问的域名
        $config->endpoint = "dysmsapi.aliyuncs.com";
        return new Dysmsapi($config);
    }

    public function sendSms(string $phone, string $signName, string $templateCode, array $templateParam): bool
    {
        if (empty($phone) || empty($signName) || empty($templateCode) || empty($templateParam)) {
            return false;
        }
        $sendSmsRequest = new SendSmsRequest(
            [
                "phoneNumbers" => $phone,
                "signName" => $signName,
                "templateCode" => $templateCode,
                "templateParam" => json_encode($templateParam)
            ]
        );
        $runtime = new RuntimeOptions([]);
        /**
         * @var $sendRes SendSmsResponse
         */
        $sendRes = $this->client->sendSmsWithOptions($sendSmsRequest, $runtime);
        if ($sendRes->body->code == 'OK') {
            return true;
        } else {
            return false;
        }
    }

}