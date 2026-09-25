@php($effectiveDate = setting('legal_effective_date', '24 September 2026'))
@php($nib = setting('nib_number'))
@php($address = setting('registered_address'))
@php($email = setting('contact_email', 'admin@kopirider.com'))

<p class="legal-meta">Version 1.0 · Effective {{ $effectiveDate }}</p>

<p>
  These terms apply when you book Kopi Rider for a wedding, private party, corporate event or
  other private booking, including our Rooftop Date experience. Markets and festivals booked under
  our free option (no truck fee, with a minimum sales guarantee) are covered by a separate written
  organiser agreement instead.
</p>
<p>
  @if ($nib)
    Kopi Rider is registered in Indonesia under business identification number (NIB) {{ $nib }}.
  @else
    Kopi Rider is registered in Indonesia (business identification number available on request).
  @endif
  @if ($address)
    Registered address: {{ $address }}.
  @endif
  In these terms, “Kopi Rider”, “we” and “us” mean Kopi Rider.
  Contact: <a href="mailto:{{ $email }}">{{ $email }}</a>, or use the WhatsApp button on our website.
</p>

<h3>1. Who can book</h3>
<p><strong>1.1</strong> You must be at least 18 years old and legally able to enter into a contract.</p>
<p><strong>1.2</strong> If you book for a group or for someone else (for example as a wedding planner), you confirm that you are allowed to do so, and you make sure your guests follow these terms.</p>

<h3>2. How booking works</h3>
<p><strong>2.1</strong> Checking a date on our website or sending a request does not reserve the date. Dates shown as available are a guide only.</p>
<p><strong>2.2</strong> A date is only available once we have confirmed it to you in writing on WhatsApp, together with your final quote.</p>
<p><strong>2.3</strong> Your booking is confirmed only when you have paid the 50% deposit.</p>
<p><strong>2.4</strong> For dates less than 48 hours away, we can only accept a booking if we can still arrange staff and ingredients. Bookings made less than 6 hours before the event cost 50% extra. We always tell you the full price before you pay.</p>

<h3>3. Prices and payment</h3>
<p><strong>3.1</strong> All prices are in Indonesian Rupiah (IDR) and include tax. Prices on our website are starting prices. Your price is the one in your written quote.</p>
<p><strong>3.2</strong> To confirm your booking, you pay a 50% deposit. The remaining 50% is due 14 days before your event. If you book less than 14 days before your event the full price will be due on the date the reservation is confirmed.</p>
<p><strong>3.3</strong> If guests are served food on the rooftop deck, a service fee of 10–50% is added for bringing the food up, depending on the time of day and event.</p>
<p><strong>3.4</strong> You pay the deposit and the rest through our payment provider Midtrans or by bank transfer, as shown on your invoice or payment link. At events where we sell directly to guests, guests pay at the truck through our point-of-sale system (Moka).</p>

<h3>4. Travel surcharge</h3>
<p>We bring the truck to you. The travel surcharge depends on where the truck is the day before your event, so we calculate it for each booking. It is shown in your quote before you pay.</p>

<h3>5. Food, allergies and halal</h3>
<p><strong>5.1</strong> Please confirm your final menu and guest numbers at least one week before your event. If you book less than a week before, please confirm them when you book. We buy ingredients for your date, so food orders cannot change after that.</p>
<p><strong>5.2</strong> Our sandwiches are made with pre-cooked meat that is reheated and assembled on the truck. Cakes come from a partner bakery.</p>
<p><strong>5.3</strong> Allergens are listed on our menu. If you or your guests have allergies or special dietary needs, you must tell us in writing, with clear and specific instructions, when you confirm your menu. We can only take allergies into account if you tell us — if you do not, we are not responsible for allergic reactions. For special requests we can put up a sign to inform your guests, and guests can always ask our staff.</p>
<p><strong>5.4</strong> We do not use pork or pork-derived ingredients. We do not serve food from the truck until our halal certification is in place.</p>

<h3>6. Rooftop Date</h3>
<p><strong>6.1</strong> A Rooftop Date is a private experience at a location we agree with you. We set up before you arrive.</p>
<p><strong>6.2</strong> Some or all of the food for a Rooftop Date is prepared by a partner restaurant. We tell you which items. Clause 5.3 also applies to this food.</p>
<p><strong>6.3</strong> Either we send you a location, or you send us the location you would like. If you choose the location, your booking is only confirmed once we have confirmed that we can operate there.</p>
<p><strong>6.4</strong> If a location we chose can no longer be used (for example because access is blocked or permission is withdrawn), we offer you another location, a new date, or a refund of what you have paid. If a location you chose cannot be used, clause 10.2 applies.</p>

<h3>7. If you cancel</h3>
<p><strong>7.1</strong> If you cancel 14 days or more before your event, we refund what you have paid, minus any costs we have already spent on your booking — for example a fee for a parking spot or location, or staff time spent checking your location.</p>
<p><strong>7.2</strong> If you cancel 13 days or less before your event, or do not show up, nothing is refunded. From that point we have kept the date for you and may have turned down other bookings.</p>
<p><strong>7.3</strong> If you book less than 14 days before your event, your payment cannot be refunded, because the date is blocked for you from the moment you book.</p>

<h3>8. If we cancel</h3>
<p><strong>8.1</strong> If we cancel for a reason within our control, you choose: a new date or a full refund of everything you have paid.</p>
<p><strong>8.2</strong> If we cancel because of an event outside our control (clause 9), we refund everything you have paid, minus the value of any part of the service already delivered.</p>

<h3>9. Weather and events outside our control</h3>
<p><strong>9.1</strong> We serve at ground level in light rain. The rooftop deck closes in rain, strong wind or lightning, because wet steel stairs are slippery. Our staff decide when the deck is safe.</p>
<p><strong>9.2</strong> If bad weather is expected, we tell you as soon as we know. We aim to let you know 2 days before your event, based on the weather forecast.</p>
<p><strong>9.3</strong> If the rooftop deck has to close because of the weather and the rooftop was the main reason you booked us, you can choose: (a) service at ground level, with 20% of the price refunded; or (b) a refund of what you have paid. This only applies if you told us in writing, when you booked, that you chose us because of the rooftop deck.</p>
<p><strong>9.4</strong> Neither of us is responsible for failing to perform because of events outside reasonable control, such as severe weather, flooding, volcanic ash, Nyepi (the Balinese Day of Silence) and related restrictions, ceremonies and road closures, government restrictions, curfews or local noise rules, fire, accidents or a serious vehicle breakdown. If such an event prevents your booking, we refund what you have paid, minus the value of any part of the service already delivered.</p>

<h3>10. Your venue</h3>
<p><strong>10.1</strong> For event bookings, you make sure that: (a) there is a firm, level area of at least 6 × 6 metres for the truck and setup; (b) the road to the location is at least 2 metres wide; (c) we can get to the site at least 4 hours before service starts, to set up; and (d) you have all permissions needed from the venue, the landowner and, where needed, the local banjar.</p>
<p><strong>10.2</strong> If the road or the setup area is too small, or we cannot operate because of anything in clause 10.1, we will leave and you will not get a refund. The space is essential for our service, and we could have set up somewhere else that day.</p>

<h3>11. Rooftop deck and safety</h3>
<p><strong>11.1</strong> No more than 7 people, or 490 kg in total, may be on the rooftop deck at the same time — whichever limit is reached first.</p>
<p><strong>11.2</strong> Please follow our <a href="{{ route('packages') }}#rooftop-safety">Rooftop Deck Safety Rules</a> and our staff’s instructions. If the rules are not followed, we will close the deck for everyone’s safety.</p>
<p><strong>11.3</strong> Children may use the deck only with an adult at all times.</p>
<p><strong>11.4</strong> Anyone who appears intoxicated will not be allowed on the deck.</p>
<p><strong>11.5</strong> You are responsible for damage to the truck or equipment caused by your guests beyond normal use.</p>
<p><strong>11.6</strong> The truck and its rooftop deck are private property. If someone does not follow our rules or our staff’s instructions, we have the right to act accordingly — including, if necessary, asking the police for help.</p>

<h3>12. Photos and video</h3>
<p><strong>12.1</strong> We would love to share your event. We only publish photos or video in which people can be recognised if they have given us written consent — for example by ticking the photo box when booking.</p>
<p><strong>12.2</strong> If you do not want photos or video taken at your event, you must tell us in writing.</p>

<h3>13. Our responsibility</h3>
<p><strong>13.1</strong> We provide our services with reasonable care and skill.</p>
<p><strong>13.2</strong> As far as Indonesian law allows, we are not responsible for indirect or consequential loss, and our total liability for a booking is limited to the total price of that booking.</p>
<p><strong>13.3</strong> Nothing in these terms limits or excludes our liability for death or personal injury caused by our negligence, for fraud, or any other liability that cannot be limited or excluded under Indonesian law, including Law No. 8 of 1999 on Consumer Protection. Any term that is not allowed under that law does not apply.</p>

<h3>14. Complaints and disputes</h3>
<p><strong>14.1</strong> If something is not right, please tell us straight away by email at <a href="mailto:{{ $email }}">{{ $email }}</a> or through the WhatsApp button on our website, so we can fix it.</p>
<p><strong>14.2</strong> These terms are governed by the laws of the Republic of Indonesia.</p>
<p><strong>14.3</strong> We will first try to solve any dispute together. If we cannot, you may take the matter to the Consumer Dispute Settlement Body (BPSK) or to the competent court in Indonesia.</p>

<h3>15. Changes to these terms</h3>
<p>We may update these terms. The version that applies to your booking is the one in force on the date your booking is confirmed.</p>
