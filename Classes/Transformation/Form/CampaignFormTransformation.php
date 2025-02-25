<?php
declare(strict_types = 1);
namespace Bitmotion\Mautic\Transformation\Form;

/**
 * {
 *   "isPublished": true,
 *   "dateAdded": "2018-10-08T12:52:28+00:00",
 *   "dateModified": "2018-10-08T13:19:42+00:00",
 *   "createdBy": 6,
 *   "createdByUser": "Florian Wessels",
 *   "modifiedBy": 6,
 *   "modifiedByUser": "Florian Wessels",
 *   "id": 49,
 *   "name": "Kampaginenform",
 *   "alias": "kampaginen",
 *   "category": null,
 *   "description": null,
 *   "cachedHtml": "",
 *   "publishUp": null,
 *   "publishDown": null,
 *   "actions": [],
 *   "template": null,
 *   "inKioskMode": false,
 *   "renderStyle": true,
 *   "formType": "campaign",
 *   "postAction": "return",
 *   "postActionProperty": null,
 *   "fields": []
 * }
 */
class CampaignFormTransformation extends AbstractFormTransformation
{
    protected $formType = 'campaign';
}
