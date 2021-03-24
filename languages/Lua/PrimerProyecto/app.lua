-- app.lua
local lapis = require("lapis")

local app = lapis.Application()
app:enable("etlua")
app.layout = require "views.layout"

app:get("/", function(self)
  return { render = "index" }
end)

app:post("/xmlresult", function(self)
  
  return { render = "xmlresult" }
end)

return app
