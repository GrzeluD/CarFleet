const Ziggy = {"url":"http:\/\/localhost","port":null,"defaults":{},"routes":{"sanctum.csrf-cookie":{"uri":"sanctum\/csrf-cookie","methods":["GET","HEAD"]},"ignition.healthCheck":{"uri":"_ignition\/health-check","methods":["GET","HEAD"]},"ignition.executeSolution":{"uri":"_ignition\/execute-solution","methods":["POST"]},"ignition.updateConfig":{"uri":"_ignition\/update-config","methods":["POST"]},"dashboard":{"uri":"dashboard","methods":["GET","HEAD"]},"profile.edit":{"uri":"profile","methods":["GET","HEAD"]},"profile.update":{"uri":"profile","methods":["PATCH"]},"profile.destroy":{"uri":"profile","methods":["DELETE"]},"vehicles.index":{"uri":"vehicles","methods":["GET","HEAD"]},"vehicles.create":{"uri":"vehicles\/create","methods":["GET","HEAD"]},"vehicles.store":{"uri":"vehicles","methods":["POST"]},"vehicles.show":{"uri":"vehicles\/{vehicle}","methods":["GET","HEAD"],"parameters":["vehicle"]},"vehicles.edit":{"uri":"vehicles\/{vehicle}\/edit","methods":["GET","HEAD"],"parameters":["vehicle"]},"vehicles.update":{"uri":"vehicles\/{vehicle}","methods":["PUT","PATCH"],"parameters":["vehicle"]},"vehicles.destroy":{"uri":"vehicles\/{vehicle}","methods":["DELETE"],"parameters":["vehicle"]},"cost-types.index":{"uri":"cost-types","methods":["GET","HEAD"]},"cost-types.create":{"uri":"cost-types\/create","methods":["GET","HEAD"]},"cost-types.store":{"uri":"cost-types","methods":["POST"]},"cost-types.show":{"uri":"cost-types\/{cost_type}","methods":["GET","HEAD"],"parameters":["cost_type"]},"cost-types.edit":{"uri":"cost-types\/{cost_type}\/edit","methods":["GET","HEAD"],"parameters":["cost_type"]},"cost-types.update":{"uri":"cost-types\/{cost_type}","methods":["PUT","PATCH"],"parameters":["cost_type"]},"cost-types.destroy":{"uri":"cost-types\/{cost_type}","methods":["DELETE"],"parameters":["cost_type"]},"users.index":{"uri":"users","methods":["GET","HEAD"]},"users.create":{"uri":"users\/create","methods":["GET","HEAD"]},"users.store":{"uri":"users","methods":["POST"]},"users.show":{"uri":"users\/{user}","methods":["GET","HEAD"],"parameters":["user"]},"users.edit":{"uri":"users\/{user}\/edit","methods":["GET","HEAD"],"parameters":["user"]},"users.update":{"uri":"users\/{user}","methods":["PUT","PATCH"],"parameters":["user"]},"users.destroy":{"uri":"users\/{user}","methods":["DELETE"],"parameters":["user"]},"register":{"uri":"register","methods":["GET","HEAD"]},"login":{"uri":"login","methods":["GET","HEAD"]},"password.request":{"uri":"forgot-password","methods":["GET","HEAD"]},"password.email":{"uri":"forgot-password","methods":["POST"]},"password.reset":{"uri":"reset-password\/{token}","methods":["GET","HEAD"],"parameters":["token"]},"password.store":{"uri":"reset-password","methods":["POST"]},"verification.notice":{"uri":"verify-email","methods":["GET","HEAD"]},"verification.verify":{"uri":"verify-email\/{id}\/{hash}","methods":["GET","HEAD"],"parameters":["id","hash"]},"verification.send":{"uri":"email\/verification-notification","methods":["POST"]},"password.confirm":{"uri":"confirm-password","methods":["GET","HEAD"]},"password.update":{"uri":"password","methods":["PUT"]},"logout":{"uri":"logout","methods":["POST"]}}};
if (typeof window !== 'undefined' && typeof window.Ziggy !== 'undefined') {
  Object.assign(Ziggy.routes, window.Ziggy.routes);
}
export { Ziggy };