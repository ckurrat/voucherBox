voucherBox is a SaaS application allowing an automatic management of guest accounts for WiFi
networks secured with with web auth.

Each voucher is time-limited, with three predefined duration (1 day, 1 week, 1 month). The voucher time
starts upon first use, hence vouchers can be pre-generated and made available for auto-provision.

When in need of a voucher, each person belonging to the hosting organisation can request a voucher; 
the voucher is sent via email to the specified email address and the code can then be used by the same person or
handed over to a guest if needed.

The voucherbox acts as AAA server for a RADIUS client implementing the web auth security. The authentication is 
implemented via a custom configured FreeRADIUS, using a SQL backend. The voucher expiration is also enforced via FreeRADIUS.

The voucher code is interpreted as the username to validate, the password can be anything as it's ignored by the
AAA server.


